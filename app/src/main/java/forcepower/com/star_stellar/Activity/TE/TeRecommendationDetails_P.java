package forcepower.com.star_stellar.Activity.TE;

import android.Manifest;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Color;
import android.media.MediaScannerConnection;
import android.net.Uri;
import android.os.Build;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;

import androidx.annotation.Nullable;
import androidx.appcompat.app.AlertDialog;
import androidx.core.app.ActivityCompat;

import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.MySiteDetailsAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.ProductListAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.MarshMallowPermission;
import forcepower.com.star_stellar.R;
import my_crop.vola.ImagePickerActivity;

import static forcepower.com.star_stellar.Class.AllUrl.show_product_list;
import static forcepower.com.star_stellar.Class.AllUrl.ws_confirm_recommended_site_for_te;
import static forcepower.com.star_stellar.Class.AllUrl.ws_reject_recommended_site_for_te;
import static forcepower.com.star_stellar.Class.AllUrl.ws_send_mail_to_asm_for_confirm_site_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.REQUEST_IMAGE;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.isValidMobile;
import static forcepower.com.star_stellar.Class.CommonClass.msg_Dialog;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.product_data;
import static forcepower.com.star_stellar.Class.CommonClass.reload;
import static forcepower.com.star_stellar.Class.CommonClass.setListViewHeightBasedOnItems;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.MarshMallowPermission.CAMERA_PERMISSION_REQUEST_CODE;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;

public class TeRecommendationDetails_P extends BaseActivity
{
    private Activity myActivity;
    private String json_row = "", r_site_id = "", expected_product_id = "";
    private ListView lv_recom_details, lv_te_eng;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();
    private ArrayList<CommonHelper> eng_details_list = new ArrayList<>();
    private MySiteDetailsAdapter myAdapter, engAdapter;
    private EditText et_site_comments, et_Select_product, et_Consumption, et_Name, et_Area,
            et_Contact_Number;
    private ArrayList<CommonHelper> product_list = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState)
    {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.te_recommendation_pending);

        try
        {
            myActivity = TeRecommendationDetails_P.this;
            //for Runtime permission
            mMP = new MarshMallowPermission(myActivity);
            //Header_View
            final RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Recommendation Site Details");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            json_row = getIntent().getStringExtra("recommended_site_details");

            final JSONObject e = new JSONObject(json_row);
            String r_recomended_site_image_url = e.getString("r_recomended_site_image_url");
            print_Log_d("r_recomended_site_image_url", r_recomended_site_image_url);
            ImageView iv_mySite_ = (ImageView) findViewById(R.id.iv_mySite_);
            lv_recom_details = (ListView) findViewById(R.id.lv_recom_details);
            lv_te_eng = (ListView) findViewById(R.id.lv_te_eng);
            myAdapter = new MySiteDetailsAdapter(this, menu_item_list);
            engAdapter = new MySiteDetailsAdapter(this, eng_details_list);
            lv_recom_details.setAdapter(myAdapter);
            lv_te_eng.setAdapter(engAdapter);

            Glide.with(myActivity).load(r_recomended_site_image_url)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .dontAnimate()
                    .error(R.drawable.default_)
                    .into(iv_mySite_);

            parse_eng_details(json_row);
            parse_mySite(json_row);
            et_site_comments = (EditText) findViewById(R.id.et_site_comments);
            et_Select_product = (EditText) findViewById(R.id.et_Select_product);
            et_Consumption = (EditText) findViewById(R.id.et_Consumption);
            et_Name = (EditText) findViewById(R.id.et_Name);
            et_Area = (EditText) findViewById(R.id.et_Area);
            et_Contact_Number = (EditText) findViewById(R.id.et_Contact_Number);
            et_Select_product.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    expectedProd_Dialog();
                }
            });

            if(!product_data.matches(""))
            {
                parseProdData(product_data);
            }
            else if(isInternetConnected(myActivity))
            {
                getProductList();
            }
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    public void expectedProd_Dialog()
    {
        try
        {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Expected Product to be Used");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin*2, 0, margin*2);
            issueBuilder.setCustomTitle(tvCPopup);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_list_view, null));

            final Dialog dialog = issueBuilder.create();
            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
            dialog.show();

            ListView lv_dialog = (ListView) dialog.findViewById(R.id.lv_dialog);
            ProductListAdapter myAdapter = new ProductListAdapter(myActivity, product_list);
            lv_dialog.setAdapter(myAdapter);
            lv_dialog.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    dialog.dismiss();
                    TextView tv_hidden_value = (TextView) view.findViewById(R.id.tv_hidden_value);
                    TextView tv_Menu_item = (TextView) view.findViewById(R.id.tv_Menu_item);
                    et_Select_product.setText(tv_Menu_item.getText().toString());
                    expected_product_id = tv_hidden_value.getText().toString(); //prod_id
                }
            });


        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    private void parse_mySite(final String str)
    {
        try
        {
            final JSONObject e = new JSONObject(str);
            menu_item_list = new ArrayList<>();

            CommonHelper cdh;

            if(e.has("r_site_name"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Site name");
                cdh.setItem1(e.getString("r_site_name"));
                menu_item_list.add(cdh);
            }

            if(e.has("r_address"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Site Address");
                cdh.setItem1(e.getString("r_address"));
                menu_item_list.add(cdh);
            }
            if(e.has("r_site_potential_in_mt"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Site MT");//replaced potential
                cdh.setItem1(e.getString("r_site_potential_in_mt"));
                menu_item_list.add(cdh);
            }
            if(e.has("r_contact_person_name"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Contact Person");
                cdh.setItem1(e.getString("r_contact_person_name"));
                menu_item_list.add(cdh);
            }
            if(e.has("r_contact_person_category_name"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Category");
                cdh.setItem1(e.getString("r_contact_person_category_name"));
                menu_item_list.add(cdh);
            }
            if(e.has("r_mobile_no"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Mobile");
                cdh.setItem1(e.getString("r_mobile_no"));
                menu_item_list.add(cdh);
            }

            //
            if(e.has("actual_product_name"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Actual Product Name");
                cdh.setItem1(e.getString("actual_product_name"));
                menu_item_list.add(cdh);
            }
            if(e.has("actual_consumption"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Actual Consumption");
                cdh.setItem1(e.getString("actual_consumption"));
                menu_item_list.add(cdh);
            }
            if(e.has("purchased_from"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Purchased From");
                cdh.setItem1(e.getString("purchased_from"));
                menu_item_list.add(cdh);
            }
            if(e.has("purchased_from_name"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("3. Purchased From Name");
                cdh.setItem1(e.getString("purchased_from_name"));
                menu_item_list.add(cdh);
            }
            if(e.has("purchased_from_area"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("4. Purchased From Area");
                cdh.setItem1(e.getString("purchased_from_area"));
                menu_item_list.add(cdh);
            }
            if(e.has("purchased_from_contact_no"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("5. Purchased From Contact No");
                cdh.setItem1(e.getString("purchased_from_contact_no"));
                menu_item_list.add(cdh);
            }

            r_site_id = e.optString("r_site_id");
            myAdapter.setFilter(menu_item_list);
            setListViewHeightBasedOnItems(lv_recom_details);
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    private void parse_eng_details(final String str)
    {
        try
        {
            final JSONObject e = new JSONObject(str);
            eng_details_list = new ArrayList<>();

            CommonHelper cdh;

            if(e.has("r_recomended_by"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Recommended By");
                cdh.setItem1(e.getString("r_recomended_by"));
                eng_details_list.add(cdh);
            }

            if(e.has("r_contact_no"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Contact No");
                cdh.setItem1(e.getString("r_contact_no"));
                eng_details_list.add(cdh);
            }
            if(e.has("r_email"))
            {
                cdh = new CommonHelper();
                cdh.setItem0("Email");
                cdh.setItem1(e.getString("r_email"));
                eng_details_list.add(cdh);
            }

            r_site_id = e.optString("r_site_id");
            engAdapter.setFilter(eng_details_list);
            setListViewHeightBasedOnItems(lv_te_eng);
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    @Override
    public void onBackPressed()
    {
        finish();
    }

    MarshMallowPermission mMP;
    public void recomm_confirm(View view)
    {
        try
        {
//            double consumption = 0;
//            try
//            {
//                if(!et_Consumption.getText().toString().matches(""))
//                {
//                    consumption = Double.parseDouble(et_Consumption.getText().toString());
//                }
//            }
//            catch (Exception e)
//            {
//                consumption = 0;
//                e.printStackTrace();
//            }

            if(et_Select_product.getText().toString().matches(""))
            {
                Toast.makeText(myActivity, "Please select a product", Toast.LENGTH_SHORT).show();
            }
//            else if(consumption == 0)
//            {
//                Toast.makeText(myActivity, "Please enter valid consumption", Toast.LENGTH_SHORT).show();
//            }
            else if(et_Name.getText().toString().matches(""))
            {
                Toast.makeText(myActivity, "Please enter name", Toast.LENGTH_SHORT).show();
            }
            else if(et_Area.getText().toString().matches(""))
            {
                Toast.makeText(myActivity, "Please enter area", Toast.LENGTH_SHORT).show();
            }
            else if(!isValidMobile(et_Contact_Number.getText().toString().trim()))
            {
                Toast.makeText(myActivity, "Please enter valid contact number", Toast.LENGTH_SHORT).show();
            }
            else if(!isInternetConnected(myActivity))
            {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
            else
            {
                if (!mMP.checkPermissionForCamera())
                {
                    mMP.requestPermissionForCamera();
                }
                else
                {
                    openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
                }
            }

        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }

    public void getProductList()
    {
        loadDialog();

        final RequestParams params = new RequestParams();
//        params.put("te_code", te_code);
//        params.put("mobile", mobile);

        final AsyncHttpClient client = new AsyncHttpClient();
           client.setTimeout(DEFAULT_TIMEOUT);
        client.post(show_product_list ,new AsyncHttpResponseHandler()
        {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
            {
                final String str = new String(responseBody);
                try
                {
                    product_data = str;
                    parseProdData(str);
                }
                catch (final Exception e)
                {
                    e.printStackTrace();
                }
                finally
                {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }
    private void parseProdData(final String str)
    {
        try
        {
            final JSONObject reader = new JSONObject(str);
            print_Log_d("product_data",str+"");

            if(reader.optString("process_status").equalsIgnoreCase("YES"))
            {
                String product_data = reader.getString("product_data");
                JSONArray ja = new JSONArray(product_data);
                product_list = new ArrayList<>();
                for(int i=0; i<ja.length(); i++)
                {
                    final JSONObject e = ja.getJSONObject(i);
                    CommonHelper commonHelper = new CommonHelper();
                    commonHelper.setItem0(e.optString("prod_id"));
                    commonHelper.setItem1(e.optString("prod_name"));

                    product_list.add(commonHelper);
                }
            }
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }

    public void continueConfirm(final File file)
    {
        try
        {
            loadDialog();

            final RequestParams params = new RequestParams();

            params.put("te_code", get_TE_code(myActivity));
            params.put("r_site_id",r_site_id);
            params.put("verified_site_image", file, "image/jpeg");
            params.put("comments", et_site_comments.getText().toString().trim());
            RadioGroup rg_dealer_sub = (RadioGroup) findViewById(R.id.rg_dealer_sub);
            final int selectedId= rg_dealer_sub.getCheckedRadioButtonId();
            RadioButton radioSexButton=(RadioButton) findViewById(selectedId);
            params.put("purchased_from", radioSexButton.getText().toString().trim());
            params.put("actual_product_id", expected_product_id);
            params.put("actual_product_name", et_Select_product.getText().toString());
            params.put("actual_consumption", et_Consumption.getText().toString());
            params.put("purchased_from_name", et_Name.getText().toString());
            params.put("purchased_from_area", et_Area.getText().toString());
            params.put("purchased_from_contact_no", et_Contact_Number.getText().toString());

            /*
            te_code,r_site_id,comments,verified_site_image,
            actual_product_id,actual_product_name,actual_consumption,purchased_from,
            purchased_from_name,purchased_from_area,purchased_from_contact_no

            Note:
            verified_site_image is image file and mandatory.
            purchased_from = Dealer/Subdealer

            After successfull confirmation the engineer will get a confirmation notification.

             */

//            print_Log_d("verified_site_image", file.getPath());
            final AsyncHttpClient client = new AsyncHttpClient();
           client.setTimeout(DEFAULT_TIMEOUT);
            print_Log_d("kereir4_U ",ws_confirm_recommended_site_for_te+"");
            print_Log_d("kereir4_P ",params+"");

            client.post(ws_confirm_recommended_site_for_te, params, new AsyncHttpResponseHandler()
            {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
                {
                    final String str = new String(responseBody);
                    try
                    {
                        final JSONObject reader = new JSONObject(str);
                        print_Log_d("kereir4_R ",str+"");
                        if(reader.optString("process_status").equalsIgnoreCase("YES"))
                        {
                            reload = true;
                            msg_Dialog(myActivity, reader.optString("process_message"), true);
                        }
                        else
                        {
                            if(reader.optString("is_show_approval_btn").equalsIgnoreCase("YES"))
                            {
                                mail_Dialog(reader.optString("process_message"), reader.optString("approval_btn_text"));
                            }
                            else
                            {
                                msg_Dialog(myActivity, reader.optString("process_message"), false);
                            }
                        }
                    }
                    catch (final Exception e)
                    {
                        print_Log_d("kereir4_E0 ",e+"");
                        e.printStackTrace();
                    }
                    finally
                    {
                        dismissDialog();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    print_Log_d("kereir4_E1 ",error+"");

                    dismissDialog();
                }


            });
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    public void mail_Dialog(final String msg, final String approval_btn_text)
    {
        try
        {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_mail, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(false);
            dialog.setCancelable(false);
            dialog.show();

            final TextView tv_submit = (TextView) dialog.findViewById(R.id.tv_submit);
            tv_submit.setText(approval_btn_text);
            final TextView tv_msg = (TextView) dialog.findViewById(R.id.tv_msg);
            tv_msg.setText(msg+"");
            tv_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    try
                    {
                        if(isInternetConnected(myActivity))
                        {
                            dialog.dismiss();
                            submit_for_approval();
                        }
                        else
                        {
                            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                        }
                    }
                    catch (Exception e)
                    {
                        e.printStackTrace();
                    }
                }
            });
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    public void submit_for_approval()
    {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));
        params.put("r_site_id", r_site_id);

        final AsyncHttpClient client = new AsyncHttpClient();
           client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_send_mail_to_asm_for_confirm_site_for_te,params, new AsyncHttpResponseHandler()
        {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
            {
                final String str = new String(responseBody);
                try
                {
                    print_Log_d("ws_send_mail_to_asm_for_confirm_site_for_te_URL ", ws_send_mail_to_asm_for_confirm_site_for_te + "");
                    print_Log_d("ws_send_mail_to_asm_for_confirm_site_for_te_PARAMS ", params.toString());
                    print_Log_d("ws_send_mail_to_asm_for_confirm_site_for_te_RESPONSE ", str + "");
                    final JSONObject reader = new JSONObject(str);
                    if(reader.optString("process_status").equalsIgnoreCase("YES"))
                    {
                        reload = true;
                    }
                    //
                    msg_Dialog(myActivity, reader.optString("process_message"), reader.optString("process_status").equalsIgnoreCase("YES"));
                }
                catch (final Exception e)
                {
                    e.printStackTrace();
                }
                finally
                {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }
    public void continueReject(View view)
    {
        try
        {
            loadDialog();

            final RequestParams params = new RequestParams();

            params.put("te_code", get_TE_code(myActivity));
            params.put("r_site_id",r_site_id);
            params.put("comments", et_site_comments.getText().toString().trim());

//te_code,r_site_id,comments
            print_Log_d("Rejected_", params.toString());


//te_code,the_engineer_id,site_name,contact_person_name,mobile_no,address,site_potential_in_mt,contact_person_category_name,recomended_site_image
            final AsyncHttpClient client = new AsyncHttpClient();
           client.setTimeout(DEFAULT_TIMEOUT);
            client.post(ws_reject_recommended_site_for_te, params, new AsyncHttpResponseHandler()
            {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody)
                {
                    final String str = new String(responseBody);
                    try
                    {
                        final JSONObject reader = new JSONObject(str);
                        print_Log_d("Rejected_ ",str+"");

                        Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        if(reader.optString("process_status").equalsIgnoreCase("YES"))
                        {
                            reload = true;
                            onBackPressed();
                        }
                    }
                    catch (final Exception e)
                    {
                        e.printStackTrace();
                    }
                    finally
                    {
                        dismissDialog();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    dismissDialog();
                }


            });
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    ProgressDialog progressDialogObj;
    public void loadDialog()
    {
        if(progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
        progressDialogObj= new ProgressDialog(myActivity);
        progressDialogObj.setCancelable(false);
        progressDialogObj.show();
    }
    public void dismissDialog()
    {
        if(progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
    }

    public void openCamera(final int codeeee)
    {
        try
        {
            Intent intent = new Intent(this, ImagePickerActivity.class);
            intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, codeeee);

            // setting aspect ratio
            intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);

            // setting maximum bitmap width and height
            intent.putExtra(ImagePickerActivity.INTENT_SET_BITMAP_MAX_WIDTH_HEIGHT, true);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_WIDTH, 1000);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_HEIGHT, 1000);

            myActivity.startActivityForResult(intent, REQUEST_IMAGE);
        }
        catch (final Exception e)
        {
            e.printStackTrace();
        }
    }
    private void requestPermission()
    {
        ActivityCompat.requestPermissions(this, new String[]{
                Manifest.permission.CAMERA
        }, CAMERA_PERMISSION_REQUEST_CODE);
    }
    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults)
    {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        switch (requestCode)
        {
            case CAMERA_PERMISSION_REQUEST_CODE:
                try
                {
                    if (!mMP.checkPermissionForCamera())
                    {
                        requestPermission();
                    }
                    else
                    {
                        openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
                    }
                }
                catch (final Exception e)
                {
                    e.printStackTrace();
                }
                break;
        }
    }
    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data)
    {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE)
        {
            if (resultCode == Activity.RESULT_OK)
            {
                try
                {
                    assert data != null;
                    final Uri uri = data.getParcelableExtra("path");
                    final File file = new File(uri.getPath());
                    if(file.exists())
                    {
                        continueConfirm(file);
                    }
                    else
                    {
                        Toast.makeText(myActivity, "Try again...", Toast.LENGTH_SHORT).show();
                    }
                }
                catch (Exception e)
                {
                    e.printStackTrace();
                }
            }
        }
    }
}

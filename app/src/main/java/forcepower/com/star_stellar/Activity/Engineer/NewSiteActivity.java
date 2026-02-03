package forcepower.com.star_stellar.Activity.Engineer;

import android.Manifest;
import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;
import androidx.appcompat.app.AlertDialog;

import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;
import java.util.ArrayList;
import java.util.List;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.ProductListAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.MarshMallowPermission;
import forcepower.com.star_stellar.R;
import my_crop.vola.ImagePickerActivity;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.show_product_list;
import static forcepower.com.star_stellar.Class.AllUrl.ws_add_site_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_contact_person_categories_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.REQUEST_IMAGE;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.contact_person_categories;
import static forcepower.com.star_stellar.Class.CommonClass.isValidMobile;
import static forcepower.com.star_stellar.Class.CommonClass.product_data;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.MarshMallowPermission.CAMERA_PERMISSION_REQUEST_CODE;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class NewSiteActivity extends BaseActivity {
    private EditText et_Site_Name, et_Contact_person, et_Mobile, et_Site_Address,
            et_Site_Potential, et_Cont_Per_Cat, et_Expected_Product, et_Consumption;
    //    private TextView et_Select_Existing, tv_or;
    private Activity myActivity;
    private ImageView iv_new_site;
    private final List<String> cont_per_list = new ArrayList<>();
    private final ArrayList<CommonHelper> product_list = new ArrayList<>();
    private String expected_product_id = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_site);
        try {
            myActivity = NewSiteActivity.this;
            mMP = new MarshMallowPermission(myActivity);

            iv_new_site = (ImageView) findViewById(R.id.iv_new_site);
//            et_Select_Existing = (TextView) findViewById(R.id.et_Select_Existing);
//            tv_or = (TextView) findViewById(R.id.tv_or);
            et_Site_Name = (EditText) findViewById(R.id.et_Site_Name);
            et_Contact_person = (EditText) findViewById(R.id.et_Contact_person);
            et_Mobile = (EditText) findViewById(R.id.et_Mobile);
            et_Site_Address = (EditText) findViewById(R.id.et_Site_Address);
            et_Site_Potential = (EditText) findViewById(R.id.et_Site_Potential);
            et_Cont_Per_Cat = (EditText) findViewById(R.id.et_Cont_Per_Cat);
            et_Expected_Product = (EditText) findViewById(R.id.et_Expected_Product);
            et_Consumption = (EditText) findViewById(R.id.et_Consumption);
            et_Cont_Per_Cat.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    contPerCat_Dialog();
                }
            });
            et_Expected_Product.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    expectedProd_Dialog();
                }
            });

            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("New Site Recommendation");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            if (!contact_person_categories.matches("")) {
                parseConPer(contact_person_categories);
            } else if (isInternetConnected(myActivity)) {
                contPerCat();
            }

            if (!product_data.matches("")) {
                parseProdData(product_data);
            } else if (isInternetConnected(myActivity)) {
                getProductList();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void contPerCat() {
        loadDialog();

        final RequestParams params = new RequestParams();
//        params.put("te_code", te_code);
//        params.put("mobile", mobile);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_contact_person_categories_for_engineer, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    contact_person_categories = str;
                    parseConPer(str);
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    public void getProductList() {
        loadDialog();

        final RequestParams params = new RequestParams();
//        params.put("te_code", te_code);
//        params.put("mobile", mobile);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(show_product_list, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    product_data = str;
                    parseProdData(str);
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    private void parseConPer(String str) {
        try {
            final JSONObject reader = new JSONObject(str);
            print_Log_d("Log_in", str + "");
//                    {"process_status":"YES","process_message":"Success","contact_person_category_data":["IHB","Mason","Contractor","Others"]}

            if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                String contact_person_category_data = reader.getString("contact_person_category_data");
                JSONArray ja = new JSONArray(contact_person_category_data);
                cont_per_list.clear();
                for (int i = 0; i < ja.length(); i++) {
                    cont_per_list.add(ja.get(i) + "");
                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void parseProdData(String str) {
        try {
            final JSONObject reader = new JSONObject(str);
            print_Log_d("product_data", str + "");

            if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                final String product_data = reader.getString("product_data");
                JSONArray ja = new JSONArray(product_data);
                product_list.clear();
                for (int i = 0; i < ja.length(); i++) {
                    final JSONObject e = ja.getJSONObject(i);
                    CommonHelper commonHelper = new CommonHelper();
                    commonHelper.setItem0(e.optString("prod_id"));
                    commonHelper.setItem1(e.optString("prod_name"));

                    product_list.add(commonHelper);
                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void continueNewSite() {
        try {
            loadDialog();

            final RequestParams params = new RequestParams();

            params.put("te_code", get_E_code(myActivity));
            params.put("the_engineer_id", get_E_id(myActivity));
            params.put("site_name", et_Site_Name.getText().toString());
            params.put("contact_person_name", et_Contact_person.getText().toString());
            params.put("mobile_no", et_Mobile.getText().toString());
            params.put("address", et_Site_Address.getText().toString());
            params.put("site_potential_in_mt", et_Site_Potential.getText().toString());
            params.put("contact_person_category_name", et_Cont_Per_Cat.getText().toString());
            params.put("expected_product_id", expected_product_id);
            params.put("expected_consumption", et_Consumption.getText().toString());


            print_Log_d("new_site_file_path", outPutFile.getPath());
            if (outPutFile.exists()) {
                params.put("recomended_site_image", outPutFile, "image/jpeg");
            }

            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);
            client.post(ws_add_site_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    final String str = new String(responseBody);
                    try {
                        final JSONObject reader = new JSONObject(str);
                        print_Log_d("ws_add_site_recommendation_for_engineer_PARMS ", params.toString());

                        print_Log_d("ws_add_site_recommendation_for_engineer_STR ", str + "");

                        Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                            onBackPressed();
                            if (outPutFile.exists())
                                outPutFile.delete();
                        }
                    } catch (final Exception e) {
                        e.printStackTrace();
                    } finally {
                        dismissDialog();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    dismissDialog();
                }


            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    ProgressDialog progressDialogObj;

    public void loadDialog() {
        if (progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
        progressDialogObj = new ProgressDialog(myActivity);
        progressDialogObj.setCancelable(false);
        progressDialogObj.show();
    }

    public void dismissDialog() {
        if (progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
    }

    private void contPerCat_Dialog() {
        try {
            final CharSequence[] items = cont_per_list.toArray(new CharSequence[cont_per_list.size()]);

            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Contact person category");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            builder.setCustomTitle(tvCPopup);

            builder.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int pos) {

                    et_Cont_Per_Cat.setText(items[pos] + "");
                }
            });
            builder.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });

            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    MarshMallowPermission mMP;
    File outPutFile = new File("");

    public void chooseYourImage(View view) {
        if (!mMP.checkPermissionForCamera()) {
            mMP.requestPermissionForCamera();
        } else {
            openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
        }
    }

    public void expectedProd_Dialog() {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Expected Product to be Used");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
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
                    et_Expected_Product.setText(tv_Menu_item.getText().toString());
                    expected_product_id = tv_hidden_value.getText().toString(); //prod_id
                }
            });


        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void openCamera(final int codeeee) {
        try {
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
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void requestPermission() {
        ActivityCompat.requestPermissions(this, new String[]{
                Manifest.permission.CAMERA
        }, CAMERA_PERMISSION_REQUEST_CODE);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        switch (requestCode) {
            case CAMERA_PERMISSION_REQUEST_CODE:
                try {
                    if (!mMP.checkPermissionForCamera()) {
                        requestPermission();
                    } else {
                        openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                }
                break;
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE) {
            if (resultCode == Activity.RESULT_OK) {
                try {
                    assert data != null;
                    final Uri uri = data.getParcelableExtra("path");
                    iv_new_site.setImageURI(uri);
                    outPutFile = new File(uri.getPath());
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }

    public void add_new_site(final View view) {
        try {
            final String val = et_Mobile.getText().toString().trim() + "";
            double consumption = 0, site_potential = 0;
            try {
                if (!et_Consumption.getText().toString().matches("")) {
                    consumption = Double.parseDouble(et_Consumption.getText().toString());
                }
            } catch (Exception e) {
                consumption = 0;
                e.printStackTrace();
            }
            try {
                if (!et_Site_Potential.getText().toString().matches("")) {
                    site_potential = Double.parseDouble(et_Site_Potential.getText().toString());
                }
            } catch (Exception e) {
                site_potential = 0;
                e.printStackTrace();
            }

            if (et_Site_Name.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter site name", Toast.LENGTH_SHORT).show();
                et_Site_Name.requestFocus();
            } else if (et_Site_Address.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter Site Address", Toast.LENGTH_SHORT).show();
                et_Site_Address.requestFocus();
            } else if (site_potential == 0) {
                Toast.makeText(myActivity, "Please enter valid Site MT", Toast.LENGTH_SHORT).show();//replaced Potential(MT)
                et_Site_Potential.requestFocus();
            } else if (et_Contact_person.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter contact person name", Toast.LENGTH_SHORT).show();
                et_Contact_person.requestFocus();
            } else if (et_Cont_Per_Cat.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter contact person category", Toast.LENGTH_SHORT).show();
                if (cont_per_list.size() == 0 && isInternetConnected(myActivity)) {
                    contPerCat();
                } else {
                    Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                }
            } else if (!isValidMobile(val)) {
                Toast.makeText(myActivity, "Please enter valid mobile number", Toast.LENGTH_SHORT).show();
                et_Mobile.requestFocus();
            } else if (et_Expected_Product.getText().toString().matches("") ||
                    expected_product_id.matches("")) {
                Toast.makeText(myActivity, "Please select expected product to be used", Toast.LENGTH_SHORT).show();
                et_Expected_Product.requestFocus();
            } else if (consumption == 0) {
                Toast.makeText(myActivity, "Please enter valid expected consumption (No. of bags)", Toast.LENGTH_SHORT).show();
                et_Consumption.requestFocus();
            }
//            else if(!outPutFile.exists())
//            {
//                Toast.makeText(myActivity, "Please capture a site image", Toast.LENGTH_SHORT).show();
//            }
            else if (!isInternetConnected(myActivity)) {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            } else {
                continueNewSite();
            }

        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}


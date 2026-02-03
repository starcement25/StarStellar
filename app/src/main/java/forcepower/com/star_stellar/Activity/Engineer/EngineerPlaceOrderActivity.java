package forcepower.com.star_stellar.Activity.Engineer;

import static forcepower.com.star_stellar.Class.AllUrl.show_product_list;
import static forcepower.com.star_stellar.Class.AllUrl.ws_save_order_query_data;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.changeDateFormat;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.dateToMilliSecond;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.msg_Dialog;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.product_data;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_server_current_date;

import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.appcompat.app.AlertDialog;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Locale;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.ProductListAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

public class EngineerPlaceOrderActivity extends BaseActivity {
    private Activity myActivity;
    private TextView et_product, et_date_of_lifting;
    private EditText et_quantity, et_remarks;
    private long current_date = 0;
    private ArrayList<CommonHelper> product_list = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_engineer_place_order);

        try {
            myActivity = this;
            //Header_View
            final RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            final LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);

            final RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            final TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("New Order Query");

            final ImageView iv_prod = (ImageView) findViewById(R.id.iv_prod);
            final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });


            final EditText et_linked_dealer = (EditText) findViewById(R.id.et_linked_dealer);
            et_product = (TextView) findViewById(R.id.et_product);
            et_date_of_lifting = (TextView) findViewById(R.id.et_date_of_lifting);
            et_quantity = (EditText) findViewById(R.id.et_quantity);
            et_remarks = (EditText) findViewById(R.id.et_remarks);
            et_linked_dealer.setText(get_TE_name(myActivity));

            final String crr_dt = changeDateFormat("yyyy-MM-dd", "yyyy-MM-dd", get_server_current_date(myActivity));
            current_date = dateToMilliSecond(crr_dt);

            et_date_of_lifting.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    show_calendar(null);
                }
            });
            iv_prod.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    expectedProd_Dialog(view);
                }
            });

            et_product.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    expectedProd_Dialog(view);
                }
            });

            final Button btn_lifting_submit = (Button) findViewById(R.id.btn_lifting_submit);
            btn_lifting_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    try {
                        int qty = 0;
                        try {
                            qty = Integer.parseInt(et_quantity.getText().toString().trim());
                        } catch (Exception e) {
                            e.printStackTrace();
                        }

                        if (et_product.getText().toString().trim().isEmpty()) {
                            msg_Dialog(myActivity, "Please select a Product", false);
                        } else if (et_quantity.getText().toString().trim().isEmpty() || qty < 1) {
                            msg_Dialog(myActivity, "Please enter quantity", false);
                        } else if (et_date_of_lifting.getText().toString().trim().isEmpty()) {
                            msg_Dialog(myActivity, "Please select Date of Requirement", false);
                        } else if (!isInternetConnected(myActivity)) {
                            msg_Dialog(myActivity, checkInternetConnection, false);
                        } else //if (continue_status.equalsIgnoreCase("yes"))
                        {
                            PostLifting();
                        }
//						else
//						{
//							new TRANS_GetLifting_Asynctask(myActivity).execute();
//						}
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });

        } catch (final Exception e) {
            e.printStackTrace();
        } finally {
            if (isInternetConnected(myActivity)) {
                getProductList();
            } else {
                msg_Dialog(myActivity, checkInternetConnection, true);
            }
        }
    }

    public void expectedProd_Dialog(final View v) {
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
                    et_product.setText(tv_Menu_item.getText().toString());
                    et_product.setTag(tv_hidden_value.getText().toString()); //prod_id
                }
            });


        } catch (final Exception e) {
            e.printStackTrace();
        }
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

    private void parseProdData(final String str) {
        try {
            final JSONObject reader = new JSONObject(str);
            print_Log_d("product_data", str + "");

            if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                String product_data = reader.getString("product_data");
                JSONArray ja = new JSONArray(product_data);
                product_list = new ArrayList<>();
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

    public void show_calendar(final View view) {
        try {
            final Calendar cldr = Calendar.getInstance();
            final int day = cldr.get(Calendar.DAY_OF_MONTH);
            final int month = cldr.get(Calendar.MONTH);
            final int year = cldr.get(Calendar.YEAR);

            // date picker dialog
            final DatePickerDialog picker_to = new DatePickerDialog(myActivity,
                    new DatePickerDialog.OnDateSetListener() {
                        @Override
                        public void onDateSet(final DatePicker datePicker, final int year, final int monthOfYear, final int dayOfMonth) {
                            final String date = year + "-" + (monthOfYear + 1) + "-" + dayOfMonth;
                            et_date_of_lifting.setText(date);
                        }
                    }, year, month, day);
            picker_to.show();

//            picker_to.getDatePicker().setMaxDate(validation_last_date);
            cldr.add(Calendar.MONTH, -1);
            picker_to.getDatePicker().setMinDate(current_date);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void PostLifting() {
        loadDialog();

        final RequestParams nameValuePairs = new RequestParams();
//						order_query_data
        nameValuePairs.put("engineer_code", get_E_id(myActivity));

        final String global_timeStamp = get_server_current_date(myActivity) + "_" + new SimpleDateFormat("HHmmss", Locale.getDefault()).format(Calendar.getInstance().getTime());
        final String t_order_id = "EN" + get_E_id(myActivity) + global_timeStamp + "" + et_product.getTag().toString();

        nameValuePairs.put("order_query_data[0][order_id]", t_order_id);
        nameValuePairs.put("order_query_data[0][linked_te_code]", get_TE_code(myActivity));
        nameValuePairs.put("order_query_data[0][dns_prod_code]", et_product.getTag().toString());
        nameValuePairs.put("order_query_data[0][prod_name]", et_product.getText().toString());
        nameValuePairs.put("order_query_data[0][qty_bags]", et_quantity.getText().toString().trim());
        nameValuePairs.put("order_query_data[0][query_date]", et_date_of_lifting.getText().toString());
        nameValuePairs.put("order_query_data[0][date_of_lifting]", get_server_current_date(myActivity));
        nameValuePairs.put("order_query_data[0][remarks]", et_remarks.getText().toString());

        print_Log_d("tr44r_P ", nameValuePairs + "");
        print_Log_d("tr44r_U ", ws_save_order_query_data);

        //the_engineer_id,page_no,the_max_date_time
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_save_order_query_data, nameValuePairs, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    print_Log_d("tr44r_R ", str + "");
                    final JSONObject jo = new JSONObject(str);
                    msg_Dialog(myActivity, jo.optString("process_message"), true);

                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }

            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                print_Log_d("tr44r_Err ", error.toString());
                dismissDialog();
            }


        });
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

    @Override
    public void onBackPressed() {
        finish();
    }
}

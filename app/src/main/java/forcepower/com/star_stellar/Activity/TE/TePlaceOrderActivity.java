package forcepower.com.star_stellar.Activity.TE;

import static forcepower.com.star_stellar.Class.AllUrl.order_query_data_status_update;
import static forcepower.com.star_stellar.Class.AllUrl.ws_order_query_data_download;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.changeDateFormat;
import static forcepower.com.star_stellar.Class.CommonClass.changeDateFormat_;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.msg_Dialog;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.widget.DatePicker;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.json.JSONArray;
import org.json.JSONObject;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.Header;
import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import forcepower.com.star_stellar.Activity.TE.Adapter.TePlaceOrderAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.HTTPUtils;
import forcepower.com.star_stellar.Class.PlaceOrderModel;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;


public class TePlaceOrderActivity extends BaseActivity {
    private Activity myActivity;
    private ArrayList<PlaceOrderModel> pending_list = new ArrayList<>();

    private TextView tv_start_date, tv_end_date;
    private String from_date = "", to_date = "";

    private boolean isLoading_p = false;
    private TePlaceOrderAdapter giftRvAdapter;
    private RecyclerView rv_Pending;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_te_place_order);

        try {
            myActivity = this;
            progressDialogObj = new ProgressDialog(myActivity);
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Order Enquiry");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            //

            tv_start_date = (TextView) findViewById(R.id.tv_start_date);
            tv_end_date = (TextView) findViewById(R.id.tv_end_date);

            rv_Pending = (RecyclerView) findViewById(R.id.rv_Pending);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            giftRvAdapter = new TePlaceOrderAdapter(pending_list, myActivity);
            rv_Pending.setAdapter(giftRvAdapter);

            final String current_date = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault()).format(new Date());
            Calendar cldr = Calendar.getInstance();
            cldr.add(Calendar.DATE, -7);  // number of days

            int dayOfMonth = cldr.get(Calendar.DAY_OF_MONTH);
            int monthOfYear = cldr.get(Calendar.MONTH);
            int year = cldr.get(Calendar.YEAR);

            String previous_date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
            previous_date = changeDateFormat_(previous_date, "dd/MM/yyyy", "MMM dd, yyyy");

            tv_start_date.setText(previous_date + "");
            tv_end_date.setText(current_date + "");

            if (isInternetConnected(myActivity)) {
                get_place_order("fresh_g");
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void show_calendar(final View view) {
        try {
            Calendar cldr = Calendar.getInstance();
            int day = cldr.get(Calendar.DAY_OF_MONTH);
            int month = cldr.get(Calendar.MONTH);
            int year = cldr.get(Calendar.YEAR);

            if (view instanceof LinearLayout) {
                try {
                    if (view.getTag().toString().matches("start_date")) {
                        String date = tv_start_date.getText().toString();

                        DateFormat formatter = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
                        cldr.setTime(formatter.parse(date));

                        day = cldr.get(Calendar.DAY_OF_MONTH);
                        month = cldr.get(Calendar.MONTH);
                        year = cldr.get(Calendar.YEAR);
                    } else {
                        String date = tv_end_date.getText().toString();

                        DateFormat formatter = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
                        cldr.setTime(formatter.parse(date));

                        day = cldr.get(Calendar.DAY_OF_MONTH);
                        month = cldr.get(Calendar.MONTH);
                        year = cldr.get(Calendar.YEAR);
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
            // date picker dialog
            final DatePickerDialog picker_to = new DatePickerDialog(myActivity,
                    new DatePickerDialog.OnDateSetListener() {
                        @Override
                        public void onDateSet(DatePicker datePicker, int year, int monthOfYear, int dayOfMonth) {
                            String date = dayOfMonth + "/" + (monthOfYear + 1) + "/" + year;
                            date = changeDateFormat_(date, "dd/MM/yyyy", "MMM dd, yyyy");
                            if (view instanceof LinearLayout) {
                                if (view.getTag().toString().matches("start_date")) {
                                    tv_start_date.setText(date);
                                } else {
                                    tv_end_date.setText(date);
                                }

                            }

                            if (tv_start_date.getText().toString().trim().matches("")) {
                                Toast.makeText(myActivity, "Please select start date.", Toast.LENGTH_SHORT).show();
                            } else if (tv_end_date.getText().toString().trim().matches("")) {
                                Toast.makeText(myActivity, "Please select end date.", Toast.LENGTH_SHORT).show();
                            } else if (!isInternetConnected(myActivity)) {
                                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                            } else {
                                get_place_order(null);
                            }
                        }
                    }, year, month, day);
            picker_to.show();
//            cldr.add(Calendar.YEAR, -18);
//            picker_to.getDatePicker().setMaxDate(cldr.getTimeInMillis());
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void get_place_order(final String type) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));

        from_date = tv_start_date.getText().toString();
        to_date = tv_end_date.getText().toString();

        params.put("start_date", changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", from_date));
        params.put("end_date", changeDateFormat("MMM dd, yyyy", "yyyy-MM-dd", to_date));

        print_Log_d("m12e4o_U ", ws_order_query_data_download);
        print_Log_d("m12e4o_P ", params.toString());

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_order_query_data_download, params, new AsyncHttpResponseHandler() {
            @SuppressLint("NotifyDataSetChanged")
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                JSONObject reader = new JSONObject();

                try {
                    pending_list.clear();
                    print_Log_d("m12e4o_R ", str);
                    reader = new JSONObject(str);
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        String order_query_data = reader.getString("order_query_data");
                        JSONArray ja = new JSONArray(order_query_data);
                        if (ja.length() > 0) {

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                final PlaceOrderModel iM = new PlaceOrderModel();
                                iM.setOrder_query_id(e.optString("order_query_id"));
                                iM.setOrder_id(e.optString("order_id"));
                                iM.setProd_name(e.optString("prod_name"));
                                iM.setQty_bags(e.optString("qty_bags"));
                                iM.setDate_and_time(e.optString("date_and_time"));
                                iM.setRssd_name(e.optString("e_name"));
                                iM.setQuery_date(e.optString("query_date"));
                                iM.setDate_of_lifting(e.optString("date_of_lifting"));
                                iM.setRemarks(e.optString("remarks"));

                                iM.setStatus_from_app(e.optString("status_from_app"));
                                iM.setStatus_remarks(e.optString("status_remarks"));
                                pending_list.add(iM);
                            }


                            giftRvAdapter = new TePlaceOrderAdapter(pending_list, myActivity);
                            rv_Pending.setAdapter(giftRvAdapter);
                        }
                    } else {
                        msg_Dialog(myActivity, reader.optString("process_message"), false);
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                msg_Dialog(myActivity, checkInternetConnection, false);

                print_Log_d("m12e4o_Err ", error.toString());

                dismissDialog();
            }


        });
    }

    public class Downloading extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private JSONObject jo = new JSONObject();
        private String order_query_id, status_from_app, status_remarks;

        public Downloading(final Activity context,
                           final String order_query_id, final String status_from_app, final String status_remarks) {
            this.mContext = context;
            this.order_query_id = order_query_id;
            this.status_from_app = status_from_app;
            this.status_remarks = status_remarks;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            loadDialog();
        }

        @Override
        public String doInBackground(String... par) {
            String POST_result = "";
            try {
                final ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);

                nameValuePairs.add(new BasicNameValuePair("order_query_id", order_query_id));
                nameValuePairs.add(new BasicNameValuePair("status_from_app", status_from_app));
                nameValuePairs.add(new BasicNameValuePair("status_remarks", status_remarks));

                final String url = order_query_data_status_update;
                POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);
                jo = new JSONObject(POST_result);

                print_Log_d("pe4w5_U ", url);
                print_Log_d("pe4w5_P ", nameValuePairs.toString());
                print_Log_d("pe4w5_R ", POST_result);
            } catch (Exception e) {
                print_Log_d("pe4w5_Err1 ", e.toString());

                e.printStackTrace();
            }

            return null;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                msg_Dialog(mContext, jo.optString("process_message"), true);
            } catch (Exception e) {
                e.printStackTrace();
            } finally {
                dismissDialog();
            }
        }
    }

    ProgressDialog progressDialogObj;

    public void loadDialog() {
        if (!progressDialogObj.isShowing())
            progressDialogObj.show();
    }

    public void dismissDialog() {
        if (progressDialogObj.isShowing())
            progressDialogObj.dismiss();
    }

    @Override
    public void onBackPressed() {
        finish();
    }
}

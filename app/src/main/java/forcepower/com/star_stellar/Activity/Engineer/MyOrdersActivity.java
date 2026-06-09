package forcepower.com.star_stellar.Activity.Engineer;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.graphics.Paint;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AbsListView;
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

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.Locale;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.MyOrderDeliveredAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.MyOrderPendingAdapter;
import forcepower.com.star_stellar.Activity.Engineer.DataSet.OrderDataSet;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.ws_confirm_order_received;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_delivered_order_history;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_pending_order_history;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class MyOrdersActivity extends BaseActivity {
    TextView tv_pending, tv_delivered, tv_no_record_p, tv_no_record_d;
    TextView tv_start_date, tv_end_date, btn_search_date;
    ListView lv_Pending, lv_delivered;
    Activity myActivity;
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();
    private ArrayList<CommonHelper> pending_list_full = new ArrayList<>();
    private ArrayList<OrderDataSet> approved_list = new ArrayList<>();
    private ArrayList<OrderDataSet> approved_list_full = new ArrayList<>();
    public static String the_max_date_time_P = "", the_max_date_time_A = "";
    public int page_no_A = 1, page_no_P = 1;

    MyOrderPendingAdapter mPendingAdapter;
    MyOrderDeliveredAdapter mDeliveredAdapter;
    LinearLayout ll_no_record_p, ll_no_record_d;
    boolean isFirstTimeLoad = true, openDelivered;

    // Date filter variables
    private String selectedStartDate = "";
    private String selectedEndDate = "";
    private SimpleDateFormat displayDateFormat = new SimpleDateFormat("dd/MM/yy", Locale.getDefault());
    private SimpleDateFormat apiDateFormat = new SimpleDateFormat("dd/MM/yy", Locale.getDefault());

    @SuppressLint("SetTextI18n")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_orders_eng);

        try {
            myActivity = MyOrdersActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = findViewById(R.id.tvCaption);
            tvCaption.setText("My Orders");
            ImageView ivBack = findViewById(R.id.ivBack);
            ivBack.setOnClickListener(v -> onBackPressed());

            ll_no_record_p = findViewById(R.id.ll_no_record_p);
            ll_no_record_d = findViewById(R.id.ll_no_record_d);
            lv_delivered = findViewById(R.id.lv_delivered);
            lv_Pending = findViewById(R.id.lv_Pending);
            tv_pending = findViewById(R.id.tv_pending);
            tv_delivered = findViewById(R.id.tv_delivered);

            // Date filter views
//            tv_start_date = findViewById(R.id.tv_start_date);
//            tv_end_date = findViewById(R.id.tv_end_date);
//            btn_search_date = findViewById(R.id.btn_search_date);

            tv_no_record_p = findViewById(R.id.tv_no_record_p);
            tv_no_record_p.setPaintFlags(tv_no_record_p.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            tv_no_record_d = findViewById(R.id.tv_no_record_d);
            tv_no_record_d.setPaintFlags(tv_no_record_d.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);

            // Setup date pickers
           // setupDatePickers();

            the_max_date_time_P = "";
            the_max_date_time_A = "";
            mPendingAdapter = new MyOrderPendingAdapter(myActivity, pending_list);
            lv_Pending.setAdapter(mPendingAdapter);
            lv_Pending.setOnItemClickListener((adapterView, view, i, l) -> {
                //
            });
            lv_Pending.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    if (!pending_list.isEmpty()) {
                        if (lv_Pending.getLastVisiblePosition() == lv_Pending.getAdapter().getCount() - 1
                                && lv_Pending.getChildAt(lv_Pending.getChildCount() - 1).getBottom() == lv_Pending.getHeight()
                                && page_no_P != -1) {
                            print_Log_d("SCROLLING_DOWN", "PENDING");
                            if (isInternetConnected(myActivity)) {
                                //get_Pending_List(page_no_P, the_max_date_time_P, "add_p");
                            }
                        }
                    }
                }
            });

            mDeliveredAdapter = new MyOrderDeliveredAdapter(myActivity, approved_list);
            lv_delivered.setAdapter(mDeliveredAdapter);
            lv_delivered.setOnItemClickListener((adapterView, view, i, l) -> {
                //
            });
            lv_delivered.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    if (!approved_list.isEmpty()) {
                        if (lv_delivered.getLastVisiblePosition() == lv_delivered.getAdapter().getCount() - 1
                                && lv_delivered.getChildAt(lv_delivered.getChildCount() - 1).getBottom() == lv_Pending.getHeight()
                                && page_no_A != -1) {
                            print_Log_d("SCROLLING_DOWN", "APPROVED");
                            if (isInternetConnected(myActivity)) {
                                get_Approve_List(page_no_A, the_max_date_time_A, "add_p");
                            }
                        }
                    }
                }
            });
            //
            if (isInternetConnected(myActivity)) {
                //get_Pending_List(page_no_P, the_max_date_time_P, "fresh_p");
                get_Approve_List(page_no_A, the_max_date_time_A, "fresh_a");
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
            openDelivered = getIntent().getBooleanExtra("openDelivered", false);
            Log.d("ppppp", String.valueOf(openDelivered));

                deliveredList(null);

        } catch (final Exception ignored) {
        }
    }

//    private void setupDatePickers() {
//        tv_start_date.setOnClickListener(v -> {
//            Log.d("DateFilter", "Start date clicked");
//            showStartDatePicker();
//        });
//        tv_end_date.setOnClickListener(v -> {
//            Log.d("DateFilter", "End date clicked");
//            showEndDatePicker();
//        });
//        btn_search_date.setOnClickListener(v -> {
//            Log.d("DateFilter", "Search button clicked");
//            performDateSearch();
//        });
//    }

    // Public method for XML onClick attribute
    public void searchByDate(View view) {
        Log.d("DateFilter", "searchByDate called from XML");
        performDateSearch();
    }

    private void showStartDatePicker() {
        Calendar calendar = Calendar.getInstance();
        DatePickerDialog datePickerDialog = new DatePickerDialog(
                myActivity,
                (view, year, month, dayOfMonth) -> {
                    calendar.set(year, month, dayOfMonth);
                    selectedStartDate = apiDateFormat.format(calendar.getTime());
                    tv_start_date.setText(displayDateFormat.format(calendar.getTime()));
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        );
        datePickerDialog.show();
    }

    private void showEndDatePicker() {
        Calendar calendar = Calendar.getInstance();
        DatePickerDialog datePickerDialog = new DatePickerDialog(
                myActivity,
                (view, year, month, dayOfMonth) -> {
                    calendar.set(year, month, dayOfMonth);
                    selectedEndDate = apiDateFormat.format(calendar.getTime());
                    tv_end_date.setText(displayDateFormat.format(calendar.getTime()));
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        );
        datePickerDialog.show();
    }

    private void performDateSearch() {
        Log.d("DateFilter", "performDateSearch called");
        Log.d("DateFilter", "Start date: " + selectedStartDate);
        Log.d("DateFilter", "End date: " + selectedEndDate);
        Log.d("DateFilter", "Pending list size: " + pending_list_full.size());
        Log.d("DateFilter", "Approved list size: " + approved_list_full.size());

        if (selectedStartDate.isEmpty() && selectedEndDate.isEmpty()) {
            Toast.makeText(myActivity, "Please select at least one date", Toast.LENGTH_SHORT).show();
            return;
        }

        // Filter the lists based on selected dates
        filterPendingListByDate();
        filterDeliveredListByDate();

        Log.d("DateFilter", "After filter - Pending: " + pending_list.size());
        Log.d("DateFilter", "After filter - Approved: " + approved_list.size());

        // Update UI based on current tab
        if (lv_Pending.getVisibility() == View.VISIBLE) {
            if (pending_list.isEmpty()) {
                ll_no_record_p.setVisibility(View.VISIBLE);
                lv_Pending.setVisibility(View.GONE);
            } else {
                ll_no_record_p.setVisibility(View.GONE);
                lv_Pending.setVisibility(View.VISIBLE);
            }
        } else {
            if (approved_list.isEmpty()) {
                ll_no_record_d.setVisibility(View.VISIBLE);
                lv_delivered.setVisibility(View.GONE);
            } else {
                ll_no_record_d.setVisibility(View.GONE);
                lv_delivered.setVisibility(View.VISIBLE);
            }
        }
    }

    private void filterPendingListByDate() {
        if (pending_list_full.isEmpty()) {
            return;
        }

        ArrayList<CommonHelper> filteredList = new ArrayList<>();

        for (CommonHelper item : pending_list_full) {
            try {
                // Parse the datetime from JSON string stored in item3
                JSONObject jsonObject = new JSONObject(item.getItem3());
                String itemDateStr = jsonObject.optString("datetime", "");

                if (isDateInRange(itemDateStr)) {
                    filteredList.add(item);
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }

        pending_list.clear();
        pending_list.addAll(filteredList);
        mPendingAdapter.updateList(filteredList);
    }

    private void filterDeliveredListByDate() {
        if (approved_list_full.isEmpty()) {
            return;
        }

        ArrayList<OrderDataSet> filteredList = new ArrayList<>();

        for (OrderDataSet item : approved_list_full) {
            String itemDateStr = item.getDateOfDeliver();

            if (isDateInRange(itemDateStr)) {
                filteredList.add(item);
            }
        }

        approved_list.clear();
        approved_list.addAll(filteredList);
        mDeliveredAdapter.updateList(filteredList);
    }

    private boolean isDateInRange(String dateStr) {
        if (dateStr == null || dateStr.isEmpty()) {
            return false;
        }

        try {
            Date itemDate = apiDateFormat.parse(dateStr);

            if (itemDate == null) {
                return false;
            }

            // If both dates are selected
            if (!selectedStartDate.isEmpty() && !selectedEndDate.isEmpty()) {
                Date startDate = apiDateFormat.parse(selectedStartDate);
                Date endDate = apiDateFormat.parse(selectedEndDate);

                return !itemDate.before(startDate) && !itemDate.after(endDate);
            }
            // If only start date is selected
            else if (!selectedStartDate.isEmpty()) {
                Date startDate = apiDateFormat.parse(selectedStartDate);
                return !itemDate.before(startDate);
            }
            // If only end date is selected
            else if (!selectedEndDate.isEmpty()) {
                Date endDate = apiDateFormat.parse(selectedEndDate);
                return !itemDate.after(endDate);
            }

            return true;
        } catch (ParseException e) {
            e.printStackTrace();
            return false;
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }

//    public void pendingList(View view) {
//        lv_delivered.setVisibility(View.GONE);
//        ll_no_record_d.setVisibility(View.GONE);
//
//        lv_Pending.setVisibility(View.VISIBLE);
//
//        tv_pending.setTextColor(getResources().getColor(R.color.colorWhite));
//        tv_delivered.setTextColor(Color.parseColor("#878787"));
//
//        if (pending_list.isEmpty()) {
//            ll_no_record_p.setVisibility(View.VISIBLE);
//        } else {
//            ll_no_record_p.setVisibility(View.GONE);
//        }
//    }

    public void deliveredList(View view) {
        ll_no_record_d.setVisibility(View.GONE);
        lv_delivered.setVisibility(View.VISIBLE);
        lv_Pending.setVisibility(View.GONE);
        ll_no_record_p.setVisibility(View.GONE);

        tv_pending.setTextColor(Color.parseColor("#878787"));
        tv_delivered.setTextColor(getResources().getColor(R.color.colorWhite));

        if (approved_list.isEmpty()) {
            if (!isFirstTimeLoad) {
                ll_no_record_d.setVisibility(View.VISIBLE);
            }
        } else {
            ll_no_record_d.setVisibility(View.GONE);
        }
    }

    public void get_Pending_List(int page_no, String the_max_date_time_P_, final String type) {
        if (type.matches("fresh_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no + "");
        params.put("the_max_date_time", the_max_date_time_P_);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_my_pending_order_history, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("my_site_P_my_orders", str);

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_P++;
                        String pending_recommendation_data = reader.getString("order_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            if (type.matches("fresh_p")) {
                                pending_list = new ArrayList<>();
                                pending_list_full = new ArrayList<>();
                            }

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("gift_title"));
                                cdh.setItem1(e.getString("gift_image_url"));
                                cdh.setItem2(e.getString("status"));
                                cdh.setItem3(e.toString());
                                cdh.setItem4(e.getString("point_taken_text"));
                                cdh.setItem5(e.getString("order_id"));
                                cdh.setItem6(e.optString("expected_delivery_date"));
                                cdh.setItem7(e.optString("amazon_order_id"));
                                cdh.setItem8(e.optString("amazon_order_link"));

                                pending_list.add(cdh);
                                pending_list_full.add(cdh);

                                if (i == 0) {
                                    the_max_date_time_P = e.optString("r_submission_date");
                                }
                            }

                            mPendingAdapter.setFilter(pending_list, type);
                            mPendingAdapter.notifyDataSetChanged();
                        }

                    } else {
                        page_no_P = -1;
                    }

                } catch (final Exception ignored) {
                } finally {
                    dismissDialog();
                }
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    if (!openDelivered) {
                        //pendingList(null);

                    }
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    if (!openDelivered) {
                        //pendingList(null);
                    }
                }
            }
        });
    }

    public void get_Approve_List(int page_no_A_, String the_max_date_time_A_, final String type) {
        if (type.matches("fresh_a"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no_A_ + "");
        params.put("the_max_date_time", the_max_date_time_A_);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_my_delivered_order_history, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("my_site_A_my_orders", str);
                    print_Log_d("my_site_A_my_orders_url", ws_show_my_delivered_order_history);

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        String order_data = reader.getString("order_data");
                        JSONArray ja = new JSONArray(order_data);
                        if (ja.length() > 0) {
                            if (type.matches("fresh_a")) {
                                approved_list.clear();
                                approved_list_full.clear();
                                page_no_A = 1; // Reset page number for fresh load
                            }

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                final OrderDataSet cdh = new OrderDataSet();

                                cdh.setProductName(e.getString("gift_title"));
                                cdh.setDate(e.getString("datetime"));
                                cdh.setProductImage(e.getString("gift_image_url"));
                                cdh.setOrderStatus(e.getString("status"));
                                cdh.setTokenPoint(e.getString("point_taken_text"));
                                cdh.setOrderId(e.getString("order_id"));
                                cdh.setDateOfDeliver(e.optString("delivery_date"));
                                cdh.setShowDeliveredButton(e.optString("is_order_received").equalsIgnoreCase("Yes"));
                                cdh.setOrderType(e.optString("s_type"));
                                cdh.setComment(e.optString("s_comment"));
                                cdh.setAcknowledgement_btn(e.optString("acknowledgement_btn"));
                                cdh.setFeedbackButton(e.optString("feedback_btn"));
                                cdh.setGiftId(e.optString("gift_id"));
                                cdh.setAmazonOrderId(e.optString("amazon_order_id"));
                                cdh.setAmazonOrderLink(e.optString("amazon_order_link"));
                                cdh.setProductPointText(e.optString("product_point_text"));
                                cdh.setTdsText(e.optString("tds_text"));
                                cdh.setTvRemarks(e.optString("remarks"));

                                approved_list.add(cdh);
                                approved_list_full.add(cdh);

                                if (i == 0) {
                                    the_max_date_time_A = e.optString("r_submission_date");
                                }
                            }

                            page_no_A++; // Increment page number after successful load

                            mDeliveredAdapter.setFilter(approved_list, type);
                            mDeliveredAdapter.notifyDataSetChanged();
                        }
                    } else {
                        page_no_A = -1;
                    }
                } catch (final Exception ignored) {
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

    public void no_pending_order_redeem_now(View view) {
        //Intent intent = new Intent(myActivity, EngGiftsActivity.class);
        Intent intent = new Intent(myActivity, EngGiftsActivity.class);
        startActivity(intent);
    }

    public void no_order_redeem_gift(View view) {
        Intent intent = new Intent(myActivity, EngGiftsActivity.class);
        startActivity(intent);
    }

    public void confirm_order_received(final String order_id, final String mVAL) {

        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("order_id", order_id);
        params.put("is_order_received", mVAL);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_confirm_order_received, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ws_confirm_order_received_4549 ", str);
                    print_Log_d("params ", params + "");

                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        finish();
                    }
                } catch (final Exception ignored) {
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
}
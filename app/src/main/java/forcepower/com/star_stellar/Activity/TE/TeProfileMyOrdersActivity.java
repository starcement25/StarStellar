package forcepower.com.star_stellar.Activity.TE;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.AbsListView;
import android.widget.AdapterView;
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

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;

import forcepower.com.star_stellar.Activity.Engineer.Adapter.MyOrderPendingAdapter;
import forcepower.com.star_stellar.Activity.TE.Adapter.MyOrderDeliveredAdapter_Profile;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_delivered_order_history;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_pending_order_history;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class TeProfileMyOrdersActivity extends BaseActivity {
    TextView tv_pending, tv_delivered, tv_no_record;
    ListView lv_Pending, lv_delivered;
    Activity myActivity;
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();
    private ArrayList<CommonHelper> approved_list = new ArrayList<>();
    public static String the_max_date_time_P = "", the_max_date_time_A = "";
    int page_no_A = 1, page_no_P = 1;

    MyOrderPendingAdapter mPendingAdapter;
    MyOrderDeliveredAdapter_Profile mDeliveredAdapter;
    boolean isFirstTimeLoad = true;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_orders_te);

        try {
            myActivity = TeProfileMyOrdersActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Gift Redeemed");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            lv_delivered = (ListView) findViewById(R.id.lv_delivered);
            lv_Pending = (ListView) findViewById(R.id.lv_Pending);
            tv_pending = (TextView) findViewById(R.id.tv_pending);
            tv_delivered = (TextView) findViewById(R.id.tv_delivered);
            tv_no_record = (TextView) findViewById(R.id.tv_no_record);

            the_max_date_time_P = "";
            the_max_date_time_A = "";
            mPendingAdapter = new MyOrderPendingAdapter(myActivity, pending_list);
            lv_Pending.setAdapter(mPendingAdapter);
            lv_Pending.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    //
                }
            });
            lv_Pending.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    //http://bansteen.blogspot.com/2012/10/android-how-to-detect-end-of-scroll.html
                    if (pending_list.size() > 0) {
                        if (lv_Pending.getLastVisiblePosition() == lv_Pending.getAdapter().getCount() - 1
                                && lv_Pending.getChildAt(lv_Pending.getChildCount() - 1).getBottom() == lv_Pending.getHeight()
                                && page_no_P != -1) {
                            //scroll end reached
                            //Write your code here
                            print_Log_d("SCROLLING_DOWN", "PENDING");
                            if (isInternetConnected(myActivity)) {
                                get_Pending_List(page_no_P, the_max_date_time_P, "add_p");
                            }
                        }
                    }
                }
            });

            mDeliveredAdapter = new MyOrderDeliveredAdapter_Profile(myActivity, approved_list);
            lv_delivered.setAdapter(mDeliveredAdapter);
            lv_delivered.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    //
                }
            });
            lv_delivered.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    //http://bansteen.blogspot.com/2012/10/android-how-to-detect-end-of-scroll.html
                    if (approved_list.size() > 0) {
                        if (lv_delivered.getLastVisiblePosition() == lv_delivered.getAdapter().getCount() - 1
                                && lv_delivered.getChildAt(lv_delivered.getChildCount() - 1).getBottom() == lv_Pending.getHeight()
                                && page_no_A != -1) {
                            //scroll end reached
                            //Write your code here
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
                get_Pending_List(page_no_P, the_max_date_time_P, "fresh_p");
                get_Approve_List(page_no_A, the_max_date_time_A, "fresh_a");
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }

    public void pendingList(View view) {
        lv_delivered.setVisibility(View.GONE);
        lv_Pending.setVisibility(View.VISIBLE);

        tv_pending.setTextColor(getResources().getColor(R.color.colorWhite));
        tv_delivered.setTextColor(Color.parseColor("#878787"));

        if (pending_list.size() == 0) {
            tv_no_record.setVisibility(View.VISIBLE);
        } else {
            tv_no_record.setVisibility(View.GONE);
        }
    }

    public void deliveredList(View view) {
        lv_delivered.setVisibility(View.VISIBLE);
        lv_Pending.setVisibility(View.GONE);

        tv_pending.setTextColor(Color.parseColor("#878787"));
        tv_delivered.setTextColor(getResources().getColor(R.color.colorWhite));

        if (approved_list.size() == 0) {
            tv_no_record.setVisibility(View.VISIBLE);
        } else {
            tv_no_record.setVisibility(View.GONE);
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
                    print_Log_d("my_site_P_TE", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_P++;
                        String pending_recommendation_data = reader.getString("order_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            pending_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("gift_title"));
                                cdh.setItem1(e.getString("gift_image_url"));
                                cdh.setItem2(e.getString("status"));
                                cdh.setItem3(e.toString());
                                cdh.setItem4(e.getString("point_taken_text"));
                                cdh.setItem5(e.getString("order_id"));
                                cdh.setItem6(e.optString("expected_delivery_date")); //expected_delivery_date

                                pending_list.add(cdh);
                                if (i == 0) {
                                    the_max_date_time_P = e.optString("r_submission_date");
                                }
                            }
                            //
//                            mPendingAdapter =  new MyOrderPendingAdapter(myActivity, pending_list);
//                            lv_Pending.setAdapter(mPendingAdapter);
                            mPendingAdapter.setFilter(pending_list, type);
                            mPendingAdapter.notifyDataSetChanged();
                        }
                    } else {
                        page_no_P = -1;
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    //first time
                    pendingList(null);
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    //first time
                    pendingList(null);
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
                    print_Log_d("my_site_A_TE", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_A++;
                        String order_data = reader.getString("order_data");
                        JSONArray ja = new JSONArray(order_data);
                        if (ja.length() > 0) {
                            approved_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("gift_title"));
                                cdh.setItem1(e.getString("gift_image_url"));
                                cdh.setItem2(e.getString("status"));
                                cdh.setItem3(e.toString());
                                cdh.setItem4(e.getString("point_taken_text"));
                                cdh.setItem5(e.getString("order_id"));
                                cdh.setItem6(e.optString("delivery_date")); //delivery_date

                                approved_list.add(cdh);
                                if (i == 0) {
                                    the_max_date_time_A = e.optString("r_submission_date");
                                }
                            }
                            //
//                            mDeliveredAdapter =  new MyOrderDeliveredAdapter(myActivity, approved_list);
//                            lv_delivered.setAdapter(mDeliveredAdapter);
                            mDeliveredAdapter.setFilter(approved_list, type);
                            mDeliveredAdapter.notifyDataSetChanged();
                        }
                    } else {
                        page_no_A = -1;
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
}

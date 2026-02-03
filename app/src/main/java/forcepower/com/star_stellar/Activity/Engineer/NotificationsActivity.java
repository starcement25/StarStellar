package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.Gravity;
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
import forcepower.com.star_stellar.Activity.Engineer.Adapter.NotificationAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.show_notifications_for_engineer;
import static forcepower.com.star_stellar.Class.AllUrl.show_notifications_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_user_type;

public class NotificationsActivity extends BaseActivity {
    private ListView lv_Ledger;
    private Activity myActivity;
    private ArrayList<CommonHelper> ledger_item_list = new ArrayList<>();
    private static String the_max_date_time_L = "";
    private int page_no_L = 1;
    private NotificationAdapter mAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_notification);

        try {
            myActivity = NotificationsActivity.this;
            //Header_View
            final RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            final LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            final RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            final TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Notification");
            final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            lv_Ledger = (ListView) findViewById(R.id.lv_Ledger);


            the_max_date_time_L = "";
            mAdapter = new NotificationAdapter(myActivity, ledger_item_list);
            lv_Ledger.setAdapter(mAdapter);
            lv_Ledger.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    //
                }
            });
            lv_Ledger.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    //http://bansteen.blogspot.com/2012/10/android-how-to-detect-end-of-scroll.html
                    if (ledger_item_list.size() > 0) {
                        if (lv_Ledger.getLastVisiblePosition() == lv_Ledger.getAdapter().getCount() - 1
                                && lv_Ledger.getChildAt(lv_Ledger.getChildCount() - 1).getBottom() == lv_Ledger.getHeight()
                                && page_no_L != -1) {
                            //scroll end reached
                            //Write your code here
                            print_Log_d("SCROLLING_DOWN", "LEDGER");
                            if (isInternetConnected(myActivity)) {
                                get_Notification_List(page_no_L, the_max_date_time_L, "add_p");
                            }
                        }
                    }
                }
            });


            //
            if (isInternetConnected(myActivity)) {
                get_Notification_List(page_no_L, the_max_date_time_L, "fresh_p");
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


    public void get_Notification_List(final int page_no, final String last_update_datetime_,
                                      final String type) {
        if (!type.matches("add_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("page_no", page_no + "");
        params.put("last_update_datetime", last_update_datetime_);

        /*  the_engineer_id,page_no,last_update_datetime

            Note:
            In response:
            m_file_type = NONE / IMAGE / PDF
            Please leave blank "last_update_datetime"
            field for first hit(Mean for page_no=1).
            If first response has data then collect n_date_time
            for 0th element and send this through
            "last_update_datetime" key for next hit
            (Mean for page_no>1).
         */

        String url = "";
        if (get_user_type(myActivity).equalsIgnoreCase("TE")) {
            params.put("te_code", get_TE_code(myActivity));
            url = show_notifications_for_te;
        } else {
            params.put("the_engineer_id", get_E_id(myActivity));
            url = show_notifications_for_engineer;
        }
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(url, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("notification_data ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_L++;
                        final String notification_data = reader.getString("notification_data");
                        final JSONArray ja = new JSONArray(notification_data);
                        if (ja.length() > 0) {
                            ledger_item_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                final CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("nid"));
                                cdh.setItem1(e.getString("m_title"));
                                cdh.setItem2(e.getString("m_message"));
                                cdh.setItem3(e.getString("m_file_type"));
                                cdh.setItem4(e.getString("m_image_link"));
                                cdh.setItem5(e.getString("n_date_time"));
                                cdh.setItem6(e.toString()); // json_row

                                ledger_item_list.add(cdh);
                                if (i == 0) {
                                    the_max_date_time_L = e.optString("n_date_time");
                                }
                            }
                            //
                            mAdapter.setFilter(ledger_item_list, type);
                        }
                    } else {
                        page_no_L = -1;
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();

                    if (ledger_item_list.size() == 0) {
                        Toast.makeText(myActivity, "No new record found.", Toast.LENGTH_SHORT).show();
                    }
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    private ProgressDialog progressDialogObj;

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

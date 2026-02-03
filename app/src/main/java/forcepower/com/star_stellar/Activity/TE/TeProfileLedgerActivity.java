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
import forcepower.com.star_stellar.Activity.TE.Adapter.TeLedgerAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_ledger_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class TeProfileLedgerActivity extends BaseActivity {
    TextView tv_no_record;
    ListView lv_Ledger;
    Activity myActivity;
    private ArrayList<CommonHelper> ledger_item_list = new ArrayList<>();
    public static String the_max_date_time_L = "";
    int page_no_L = 1;
    TeLedgerAdapter mAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_leger);

        try {
            myActivity = TeProfileLedgerActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Stellar Points");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            lv_Ledger = (ListView) findViewById(R.id.lv_Ledger);
            tv_no_record = (TextView) findViewById(R.id.tv_no_record);

            the_max_date_time_L = "";
            mAdapter = new TeLedgerAdapter(myActivity, ledger_item_list);
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
                                get_Ledger_List(page_no_L, the_max_date_time_L, "add_p");
                            }
                        }
                    }
                }
            });


            //
            if (isInternetConnected(myActivity)) {
                get_Ledger_List(page_no_L, the_max_date_time_L, "fresh_p");
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


    public void get_Ledger_List(int page_no, String the_max_date_time_L_, final String type) {
        if (!type.matches("add_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no + "");
//        params.put("the_max_date_time", the_max_date_time_L_);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_my_ledger_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ledger_data", str + "");
//{"process_status":"YES","process_message":"Success.","ledger_data":[{"ldgr_id":"1","description":"Sign up","point_earned":"100","point_redeem":"","ldgr_datetime":"01\/08\/2019"},{"ldgr_id":"2","description":"SanDik 16GB Pendrive","point_earned":"","point_redeem":"","ldgr_datetime":"02\/08\/2019"}]}
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_L++;
                        String ledger_data = reader.getString("ledger_data");
                        JSONArray ja = new JSONArray(ledger_data);
                        if (ja.length() > 0) {
                            ledger_item_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("ldgr_id"));
                                cdh.setItem1(e.getString("ldgr_datetime"));
                                cdh.setItem2(e.getString("description"));
                                cdh.setItem3(e.getString("point_earned"));
                                cdh.setItem4(e.getString("point_redeem"));
                                cdh.setItem5(e.optString("ldgr_type"));
                                cdh.setItem6(e.optString("related_data")); // related_data
                                cdh.setItem7(e.toString()); // json_row

                                ledger_item_list.add(cdh);
                                if (i == 0) {
                                    the_max_date_time_L = e.optString("r_submission_date");
                                }
                            }
                            //
//                            mAdapter =  new TeLedgerAdapter(myActivity, ledger_item_list);
//                            lv_Ledger.setAdapter(mAdapter);
                            mAdapter.setFilter(ledger_item_list, type);
                        }
                    } else {
                        page_no_L = -1;
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

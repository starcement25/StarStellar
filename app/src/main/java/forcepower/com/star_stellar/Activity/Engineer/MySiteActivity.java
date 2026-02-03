package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.View;
import android.widget.AbsListView;
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

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.MySiteApprovedAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.MySitePendingAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_approved_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_pending_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class MySiteActivity extends BaseActivity {
    private TextView tv_pending, tv_approved, tv_no_record;
    private ListView lv_Pending, lv_Approved;
    private Activity myActivity;
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();
    private ArrayList<CommonHelper> approved_list = new ArrayList<>();
    public static String the_max_date_time_P = "", the_max_date_time_A = "";
    private int page_no_A = 1, page_no_P = 1, tot_count_A = 0, tot_count_P = 0;

    private MySiteApprovedAdapter mApprovedAdapter;
    private MySitePendingAdapter mPendingAdapter;
    private boolean isFirstTimeLoad = true;
    private String tabName = "approvedList";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_site);

        try {
            myActivity = this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Recommended Site Status");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            lv_Approved = (ListView) findViewById(R.id.lv_Approved);
            lv_Pending = (ListView) findViewById(R.id.lv_Pending);
            tv_pending = (TextView) findViewById(R.id.tv_pending);
            tv_approved = (TextView) findViewById(R.id.tv_approved);
            tv_no_record = (TextView) findViewById(R.id.tv_no_record);

            the_max_date_time_P = "";
            the_max_date_time_A = "";
            mPendingAdapter = new MySitePendingAdapter(myActivity, pending_list);
            lv_Pending.setAdapter(mPendingAdapter);
            lv_Pending.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    TextView tv_json_row = (TextView) view.findViewById(R.id.tv_json_row);
                    Intent intent = new Intent(myActivity, MySiteDetailsActivity.class);
                    intent.putExtra("json_row", tv_json_row.getText().toString());
                    startActivity(intent);
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
                        try {
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
                        } catch (Exception e) {
                            e.printStackTrace();
                        }
                    }
                }
            });

            mApprovedAdapter = new MySiteApprovedAdapter(myActivity, approved_list);
            lv_Approved.setAdapter(mApprovedAdapter);
            lv_Approved.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    TextView tv_json_row = (TextView) view.findViewById(R.id.tv_json_row);
                    Intent intent = new Intent(myActivity, MySiteDetailsActivity.class);
                    intent.putExtra("json_row", tv_json_row.getText().toString());
                    startActivity(intent);
                }
            });
            lv_Approved.setOnScrollListener(new AbsListView.OnScrollListener() {
                @Override
                public void onScrollStateChanged(AbsListView absListView, int i) {

                }

                public void onScroll(AbsListView view, int firstVisibleItem,
                                     int visibleItemCount, int totalItemCount) {
                    //http://bansteen.blogspot.com/2012/10/android-how-to-detect-end-of-scroll.html
                    if (approved_list.size() > 0) {
                        try {
                            if (lv_Approved.getLastVisiblePosition() == lv_Approved.getAdapter().getCount() - 1
                                    && lv_Approved.getChildAt(lv_Approved.getChildCount() - 1).getBottom() == lv_Pending.getHeight()
                                    && page_no_A != -1) {
                                //scroll end reached
                                //Write your code here
                                print_Log_d("SCROLLING_DOWN", "APPROVED");
                                if (isInternetConnected(myActivity)) {
                                    get_Approve_List(page_no_A, the_max_date_time_A, "add_p");
                                }
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
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

            //SEARCH
            final EditText et_m_search_bar = (EditText) findViewById(R.id.et_m_search_bar);

            final ImageView iv_m_search_clear = (ImageView) findViewById(R.id.iv_m_search_clear);
            iv_m_search_clear.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    et_m_search_bar.setText("");
                }
            });

            et_m_search_bar.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence cs, int i, int i1, int i2) {
                    try {
                        if (tabName.equalsIgnoreCase("pendingList")) {
                            final ArrayList<CommonHelper> temp1 = new ArrayList<>();
                            for (int index = 0; index < pending_list.size(); index++) {
                                if (pending_list.get(index).getItem0().toLowerCase().contains(cs.toString().toLowerCase().trim()) ||
                                        pending_list.get(index).getItem5().toLowerCase().contains(cs.toString().toLowerCase().trim())) {
                                    temp1.add(pending_list.get(index));
                                }
                            }
                            //
                            mPendingAdapter.setFilter(temp1, "PPP");
                            mPendingAdapter.notifyDataSetChanged();
                        } else if (tabName.equalsIgnoreCase("approvedList")) {
                            final ArrayList<CommonHelper> temp2 = new ArrayList<>();
                            for (int index = 0; index < approved_list.size(); index++) {
                                if (approved_list.get(index).getItem0().toLowerCase().contains(cs.toString().toLowerCase().trim()) ||
                                        approved_list.get(index).getItem5().toLowerCase().contains(cs.toString().toLowerCase().trim())) {
                                    temp2.add(approved_list.get(index));
                                }
                            }
                            //
                            mApprovedAdapter.setFilter(temp2, "AAA");
                            mApprovedAdapter.notifyDataSetChanged();
                        }
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void afterTextChanged(Editable editable) {

                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }

    public void pendingList(View view) {
        tabName = "pendingList";
        lv_Approved.setVisibility(View.GONE);
        lv_Pending.setVisibility(View.VISIBLE);

        tv_pending.setTextColor(getResources().getColor(R.color.colorWhite));
        tv_approved.setTextColor(Color.parseColor("#878787"));

        if (pending_list.size() == 0) {
            tv_no_record.setVisibility(View.VISIBLE);
        } else {
            tv_no_record.setVisibility(View.GONE);
        }
    }

    public void approvedList(View view) {
        tabName = "approvedList";
        lv_Approved.setVisibility(View.VISIBLE);
        lv_Pending.setVisibility(View.GONE);

        tv_pending.setTextColor(Color.parseColor("#878787"));
        tv_approved.setTextColor(getResources().getColor(R.color.colorWhite));

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
        client.post(ws_show_pending_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("my_site_P_mysite", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_P == 1) {
                            tot_count_P = reader.optInt("tot_count");
                        }
                        page_no_P++;
                        String pending_recommendation_data = reader.getString("pending_recommendation_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            pending_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("r_site_name"));
                                cdh.setItem1(e.getString("r_submission_date_modified"));
                                cdh.setItem2(e.getString("r_status"));
                                cdh.setItem3(e.toString());
                                cdh.setItem5(e.optString("r_mobile_no")); //r_mobile_no
                                pending_list.add(cdh);
                                if (i == 0 && the_max_date_time_P.matches("")) {
                                    the_max_date_time_P = e.optString("r_submission_date");
                                }
                            }
                            //
//                            mPendingAdapter =  new MySitePendingAdapter(myActivity, pending_list);
//                            lv_Pending.setAdapter(mPendingAdapter);
                            mPendingAdapter.setFilter(pending_list, type);
                            mPendingAdapter.notifyDataSetChanged();
                            tv_pending.setText("PENDING (" + tot_count_P + ")");
                        }
                    } else {
                        page_no_P = -1;
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

    public void get_Approve_List(int page_no_A_, String the_max_date_time_A_, final String type) {
        if (type.matches("fresh_a"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no_A_ + "");
        params.put("the_max_date_time", the_max_date_time_A_);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_approved_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("my_site_A__mysite", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_A == 1) {
                            tot_count_A = reader.optInt("tot_count");
                        }
                        page_no_A++;
                        String pending_recommendation_data = reader.getString("approved_recommendation_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            approved_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("r_site_name"));
                                cdh.setItem1(e.getString("r_submission_date_modified"));
                                cdh.setItem2(e.getString("r_status"));
                                cdh.setItem3(e.toString());
                                cdh.setItem4(e.optString("point_earned")); //point_earned
                                cdh.setItem5(e.optString("r_mobile_no")); //r_mobile_no

                                approved_list.add(cdh);
                                if (i == 0 && the_max_date_time_A.matches("")) {
                                    the_max_date_time_A = e.optString("r_submission_date");
                                }
                            }
                            //
//                            mApprovedAdapter =  new MySiteApprovedAdapter(myActivity, approved_list);
//                            lv_Approved.setAdapter(mApprovedAdapter);
                            mApprovedAdapter.setFilter(approved_list, type);
                            mApprovedAdapter.notifyDataSetChanged();
                            tv_approved.setText("APPROVED (" + tot_count_A + ")");
                        }
                    } else {
                        page_no_A = -1;
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    //first time
                    approvedList(null);
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                if (isFirstTimeLoad) {
                    isFirstTimeLoad = false;
                    //first time
                    approvedList(null);
                }
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

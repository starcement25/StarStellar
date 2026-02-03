package forcepower.com.star_stellar.Activity.TE;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.TE.Adapter.ApproveAdapter_Profile;
import forcepower.com.star_stellar.Activity.TE.Adapter.OnLoadMoreListener_A;
import forcepower.com.star_stellar.Activity.TE.Adapter.OnLoadMoreListener_P;
import forcepower.com.star_stellar.Activity.TE.Adapter.OnLoadMoreListener_R;
import forcepower.com.star_stellar.Activity.TE.Adapter.PendingAdapter_Profile;
import forcepower.com.star_stellar.Activity.TE.Adapter.RejectAdapter_Profile;
import forcepower.com.star_stellar.Activity.TE.Adapter.Student;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_approved_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_pending_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_rejected_recommendation_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class TeProfileSiteRecommActivity extends BaseActivity {
    Activity myActivity;
    TextView tv_no_record, tv_pending, tv_approved, tv_rejected;
    RecyclerView rv_Pending, rv_Approved, rv_Rejected;

    public String the_max_date_time_P = "", the_max_date_time_A = "", the_max_date_time_R = "";
    int page_no_A = 1, page_no_P = 1, page_no_R = 1,
            tot_count_A = 0, tot_count_P = 0, tot_count_R = 0;

    private PendingAdapter_Profile pAdapter;
    private ApproveAdapter_Profile aAdapter;
    private RejectAdapter_Profile rAdapter;

    private List<Student> p_list = new ArrayList<>();
    private List<Student> reject_list = new ArrayList<>();
    private List<Student> approve_list = new ArrayList<>();

    protected Handler handler_p, handler_a, handler_r;
    boolean isFirstTimeLoad = true;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_site_recommended);

        try {
            myActivity = TeProfileSiteRecommActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Site Recommendations");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            tv_no_record = (TextView) findViewById(R.id.tv_no_record);
            tv_pending = (TextView) findViewById(R.id.tv_pending);
            tv_approved = (TextView) findViewById(R.id.tv_approved);
            tv_rejected = (TextView) findViewById(R.id.tv_rejected);

            rv_Pending = (RecyclerView) findViewById(R.id.rv_Pending);
            rv_Approved = (RecyclerView) findViewById(R.id.rv_Approved);
            rv_Rejected = (RecyclerView) findViewById(R.id.rv_Rejected);


            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            rv_Approved.setHasFixedSize(true);
            rv_Approved.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Approved.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            rv_Rejected.setHasFixedSize(true);
            rv_Rejected.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Rejected.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            the_max_date_time_P = the_max_date_time_A = the_max_date_time_R = "";
            page_no_A = page_no_P = page_no_R = 1;
            tot_count_A = tot_count_P = tot_count_R = 0;

            //new technology

            handler_p = new Handler();
            pAdapter = new PendingAdapter_Profile(myActivity, p_list, rv_Pending);
            rv_Pending.setAdapter(pAdapter);

            handler_a = new Handler();
            aAdapter = new ApproveAdapter_Profile(myActivity, approve_list, rv_Approved);
            rv_Approved.setAdapter(aAdapter);

            handler_r = new Handler();
            rAdapter = new RejectAdapter_Profile(myActivity, reject_list, rv_Rejected);
            rv_Rejected.setAdapter(rAdapter);


            get_Pending_List(1, "", "fresh_p");
            pAdapter.setOnLoadMoreListener(new OnLoadMoreListener_P() {
                @Override
                public void onLoadMore() {
                    if (page_no_P > 0) {
                        p_list.add(null);
                        pAdapter.notifyItemInserted(p_list.size() - 1);

                        handler_p.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                get_Pending_List(page_no_P, the_max_date_time_P, "add_p");
                                //or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
                            }
                        }, 2000);
                    }
                }
            });
            get_Approve_List(1, "", "fresh_a");
            aAdapter.setOnLoadMoreListener(new OnLoadMoreListener_A() {
                @Override
                public void onLoadMore() {
                    if (page_no_A > 0) {
                        approve_list.add(null);
                        aAdapter.notifyItemInserted(approve_list.size() - 1);

                        handler_a.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                get_Approve_List(page_no_A, the_max_date_time_A, "add_a");
                                //or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
                            }
                        }, 2000);
                    }
                }
            });
            get_Reject_List(1, "", "fresh_r");
            rAdapter.setOnLoadMoreListener(new OnLoadMoreListener_R() {
                @Override
                public void onLoadMore() {
                    //add null , so the adapter will check view_type and show progress bar at bottom
                    if (page_no_R > 0) {
                        reject_list.add(null);
                        rAdapter.notifyItemInserted(reject_list.size() - 1);

                        handler_r.postDelayed(new Runnable() {
                            @Override
                            public void run() {
                                get_Reject_List(page_no_R, the_max_date_time_R, "add_r");
                                //or you can add all at once but do not forget to call pAdapter.notifyDataSetChanged();
                            }
                        }, 2000);

                    }
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
    }

    public void get_Pending_List(int page_no, String the_max_date_time, final String type) {
        if (!type.matches("add_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no + "");
        params.put("the_max_date_time", the_max_date_time);

        //the_engineer_id,page_no,the_max_date_time
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_pending_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("recommended_p", str + "");
                    if (type.matches("add_p")) {
                        //   remove progress item
                        p_list.remove(p_list.size() - 1);
                        pAdapter.notifyItemRemoved(p_list.size());
                    } else {
                        p_list.clear();
                    }

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_P == 1) {
                            tot_count_P = reader.optInt("tot_count");
                        }
                        page_no_P++;
                        String pending_recommendation_data = reader.getString("pending_recommendation_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                p_list.add(new Student(e.getString("r_site_name"),
                                        e.optString("r_submission_date"),
                                        e.getString("r_status"),
                                        e.toString(),
                                        e.optString("point_earned")));
                                pAdapter.notifyItemInserted(p_list.size());
                                if (i == 0) {
                                    the_max_date_time_P = e.optString("r_submission_date");
                                }
                            }
                            pAdapter.setLoaded();


                            tv_pending.setText("Pending(" + tot_count_P + ")");
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

    public void get_Approve_List(int page_no, String the_max_date_time, final String type) {
        if (!type.matches("add_a"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no + "");
        params.put("the_max_date_time", the_max_date_time);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_approved_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("recommended_a", str + "");
                    if (type.matches("add_a")) {
                        //   remove progress item
                        approve_list.remove(approve_list.size() - 1);
                        aAdapter.notifyItemRemoved(approve_list.size());
                    } else {
                        approve_list.clear();
                    }

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_A == 1) {
                            tot_count_A = reader.optInt("tot_count");
                        }
                        page_no_A++;
                        String pending_recommendation_data = reader.getString("approved_recommendation_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                approve_list.add(new Student(e.getString("r_site_name"),
                                        e.optString("r_submission_date"),
                                        e.getString("r_status"),
                                        e.toString(),
                                        e.optString("point_earned")));
                                aAdapter.notifyItemInserted(approve_list.size());
                                if (i == 0) {
                                    the_max_date_time_A = e.optString("r_submission_date");
                                }
                            }
                            aAdapter.setLoaded();


                            tv_approved.setText("Approved(" + tot_count_A + ")");
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

    public void get_Reject_List(int page_no, String the_max_date_time, final String type) {
        if (!type.matches("add_r"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no + "");
        params.put("the_max_date_time", the_max_date_time);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_rejected_recommendation_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("recommended_r", str + "");
                    if (type.matches("add_r")) {
                        //   remove progress item
                        reject_list.remove(reject_list.size() - 1);
                        rAdapter.notifyItemRemoved(reject_list.size());
                    } else {
                        reject_list.clear();
                    }

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_R == 1) {
                            tot_count_R = reader.optInt("tot_count");
                        }
                        page_no_R++;
                        String rejected_recommendation_data = reader.getString("rejected_recommendation_data");
                        JSONArray ja = new JSONArray(rejected_recommendation_data);
                        if (ja.length() > 0) {
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                reject_list.add(new Student(e.getString("r_site_name"),
                                        e.optString("r_submission_date"),
                                        e.getString("r_status"),
                                        e.toString(),
                                        e.optString("point_earned")));
                                rAdapter.notifyItemInserted(reject_list.size());
                                if (i == 0) {
                                    the_max_date_time_R = e.optString("r_submission_date");
                                }
                            }
                            rAdapter.setLoaded();


                            tv_rejected.setText("Rejected(" + tot_count_R + ")");
                        }
                    } else {
                        page_no_R = -1;
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

    @Override
    public void onBackPressed() {
        finish();
    }

    public void pendingList(View view) {
        try {
            //getResources().getColor(R.color.colorWhite)
            tv_pending.setTextColor(getResources().getColor(R.color.colorWhite));
            tv_approved.setTextColor(Color.parseColor("#878787"));
            tv_rejected.setTextColor(Color.parseColor("#878787"));

            rv_Approved.setVisibility(View.GONE);
            rv_Rejected.setVisibility(View.GONE);
            rv_Pending.setVisibility(View.GONE);

            if (p_list.size() == 0) {
                tv_no_record.setVisibility(View.VISIBLE);
            } else {
                tv_no_record.setVisibility(View.GONE);
                rv_Pending.setVisibility(View.VISIBLE);
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void approvedList(View view) {
        try {
            //getResources().getColor(R.color.colorWhite)
            tv_pending.setTextColor(Color.parseColor("#878787"));
            tv_approved.setTextColor(getResources().getColor(R.color.colorWhite));
            tv_rejected.setTextColor(Color.parseColor("#878787"));

            rv_Approved.setVisibility(View.GONE);
            rv_Rejected.setVisibility(View.GONE);
            rv_Pending.setVisibility(View.GONE);

            if (approve_list.size() == 0) {
                tv_no_record.setVisibility(View.VISIBLE);
            } else {
                tv_no_record.setVisibility(View.GONE);
                rv_Approved.setVisibility(View.VISIBLE);
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void rejectedList(View view) {
        try {
            //getResources().getColor(R.color.colorWhite)
            tv_pending.setTextColor(Color.parseColor("#878787"));
            tv_approved.setTextColor(Color.parseColor("#878787"));
            tv_rejected.setTextColor(getResources().getColor(R.color.colorWhite));

            rv_Approved.setVisibility(View.GONE);
            rv_Rejected.setVisibility(View.GONE);
            rv_Pending.setVisibility(View.GONE);

            if (reject_list.size() == 0) {
                tv_no_record.setVisibility(View.VISIBLE);
            } else {
                tv_no_record.setVisibility(View.GONE);
                rv_Rejected.setVisibility(View.VISIBLE);
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

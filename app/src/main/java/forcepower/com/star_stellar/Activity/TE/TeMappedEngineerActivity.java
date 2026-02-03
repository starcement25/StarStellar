package forcepower.com.star_stellar.Activity.TE;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.os.Handler;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.Gravity;
import android.view.KeyEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
import android.widget.EditText;
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
import forcepower.com.star_stellar.Activity.Engineer.PendingEngineerActivity;
import forcepower.com.star_stellar.Activity.TE.Adapter.MyEngAdapter_New;
import forcepower.com.star_stellar.Activity.TE.Adapter.OnLoadMoreListener_MyEng;
import forcepower.com.star_stellar.Activity.TE.Adapter.Student_6;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_mapped_engineers_for_te;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_pending_engineers_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;

public class TeMappedEngineerActivity extends BaseActivity {
    private Activity myActivity;
    private List<Student_6> p_list = new ArrayList<>();

    private MyEngAdapter_New pAdapter;
    protected Handler handler_p;
    private RecyclerView rv_Pending;
    private int page_no_E = 1;
    private EditText et_Search_Eng;
    private TextView tv_pending_engineer;
    private String engineer_data = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_te_mapped_engineer);

        try {
            myActivity = TeMappedEngineerActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Mapped Engineers");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            et_Search_Eng = (EditText) findViewById(R.id.et_Search_Eng);
            //Grid
            rv_Pending = (RecyclerView) findViewById(R.id.rv_MyEngineer);
            rv_Pending.setHasFixedSize(true);
            rv_Pending.setLayoutManager(new LinearLayoutManager(myActivity));
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));

            handler_p = new Handler();
            pAdapter = new MyEngAdapter_New(myActivity, p_list, rv_Pending);
            rv_Pending.setAdapter(pAdapter);
            tv_pending_engineer = (TextView) findViewById(R.id.tv_pending_engineer);

            if (isInternetConnected(myActivity)) {
                get_Engineer_details(1, "fresh_e", "", "");
                pAdapter.setOnLoadMoreListener(new OnLoadMoreListener_MyEng() {
                    @Override
                    public void onLoadMore() {
                        if (page_no_E > 0) {
                            p_list.add(null);
                            pAdapter.notifyItemInserted(p_list.size() - 1);

                            handler_p.postDelayed(new Runnable() {
                                @Override
                                public void run() {
                                    get_Engineer_details(page_no_E, "add_e", "", "");
                                }
                            }, 2000);
                        }
                    }
                });
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
            et_Search_Eng.addTextChangedListener(new TextWatcher() {
                @Override
                public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

                }

                @Override
                public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                    if (et_Search_Eng.getText().toString().trim().length() % 3 == 0 &&
                            et_Search_Eng.getText().toString().trim().length() > 0) {
                        if (isInternetConnected(myActivity)) {
                            page_no_E = 1;
                            get_Engineer_details(page_no_E, "fresh_e", et_Search_Eng.getText().toString(), "");
                        } else {
                            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                        }
                    }
                }

                @Override
                public void afterTextChanged(Editable editable) {

                }
            });

            et_Search_Eng.setOnEditorActionListener(new TextView.OnEditorActionListener() {
                @Override
                public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                    if (actionId == EditorInfo.IME_ACTION_SEARCH) {
                        if (et_Search_Eng.getText().toString().trim().length() > 0) {
                            if (isInternetConnected(myActivity)) {
                                page_no_E = 1;
                                get_Engineer_details(page_no_E, "fresh_e", et_Search_Eng.getText().toString(), "");
                            } else {
                                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                            }
                        }
                        return true;
                    }
                    return false;
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void get_Engineer_details(int page_no_E_, final String type, final String search_term, final String status) {
        if (!type.matches("add_e"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));
        params.put("page_no", page_no_E_ + "");
        params.put("search_term", search_term);
        params.put("status", status);

//te_code,page_no,search_term,status
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_mapped_engineers_for_te, params, new AsyncHttpResponseHandler() {
            @SuppressLint("NotifyDataSetChanged")
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("my_engineer_ ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        page_no_E++;
                        String pending_recommendation_data = reader.getString("engineer_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            if (type.matches("add_e")) {
                                p_list.remove(p_list.size() - 1);
                                pAdapter.notifyItemRemoved(p_list.size());
                            } else {
                                p_list.clear();
                            }

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                p_list.add(new Student_6(
                                        e.getString("e_profile_image_url"),
                                        e.getString("e_name"),
                                        e.getString("eid"),
                                        e.getString("e_city_town"),
                                        e.getString("e_status"),
                                        e.optString("r_submission_date"),
                                        e.optString("e_mobile")));

                                if (type.matches("add_e")) {
                                    pAdapter.notifyItemInserted(p_list.size());
                                }

                            }
                            if (!type.matches("add_e")) {
                                pAdapter.notifyDataSetChanged();
                            }
                            pAdapter.setLoaded();

                        }
                    } else {
                        page_no_E = -1;
                        if (type.matches("add_e")) {
                            p_list.remove(p_list.size() - 1);
                            pAdapter.notifyItemRemoved(p_list.size());
                        }
                        if (!search_term.matches("") || !status.matches("")) {
                            Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        }
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

    @Override
    public void onResume() {
        super.onResume();
        tv_pending_engineer.setVisibility(View.GONE);
        tv_pending_engineer.setClickable(false);
        if (isInternetConnected(myActivity)) {
            get_Engineer_pending();
        }
    }


    public void get_Engineer_pending() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));

//te_code,page_no,search_term,status
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_pending_engineers_for_te, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("ws_show_pending_engineers_for_te", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        tv_pending_engineer.setVisibility(View.VISIBLE);
                        tv_pending_engineer.setText(reader.optString("total_record") + " Engineer to be approved");
                        tv_pending_engineer.setClickable(true);
                        engineer_data = str;
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

    public void open_eng_search(View view) {
        try {
            if (isInternetConnected(myActivity)) {
                Intent intent = new Intent(myActivity, TeMappedEngineerSearchActivity.class);
                intent.putExtra("action_type", view.getTag().toString());
                startActivity(intent);
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void open_eng_filter(View view) {
        open_eng_search(view);
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

    public void my_eng_reset(View view) {
//        et_Search_Eng.setText("");
//        if(isInternetConnected(myActivity))
//        {
//            page_no_E = 1;
//            get_Engineer_details(page_no_E, "fresh_e", "", "");
//        }
//        else
//        {
//            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
//        }
    }

    public void pendingEngineer(View view) {
        //engineer_data
        Intent intent = new Intent(myActivity, PendingEngineerActivity.class);
        intent.putExtra("engineer_data", engineer_data);
        startActivity(intent);
    }
}

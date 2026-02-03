package forcepower.com.star_stellar.Activity.TE;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.GridLayoutManager;
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

import cz.msebera.android.httpclient.Header;

import forcepower.com.star_stellar.Activity.TE.Adapter.TeGiftRvAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_gift_catalog_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;

import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_points;


public class TeGiftsActivity extends BaseActivity {
    Activity myActivity;
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();

    TextView tv_points;
    boolean isLoading_p = false;
    TeGiftRvAdapter giftRvAdapter;
    RecyclerView rv_Pending;
    int page_no_P = 1, tot_count_P = 0, p_array_size = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gifts);

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
            tvCaption.setText("Gift Catalogue");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

//            RelativeLayout rlForward = (RelativeLayout) findViewById(R.id.rlForward);
//            rlForward.setPadding(0, 0, 25, 0);
//            tv_points = new TextView(myActivity);
//            tv_points.setText(""+get_TE_points(myActivity));
//            tv_points.setGravity(Gravity.RIGHT);
//            tv_points.setTextColor(getResources().getColor(R.color.white));
//            rlForward.addView(tv_points);
//            rlForward.setVisibility(View.VISIBLE);
            //Grid
            rv_Pending = (RecyclerView) findViewById(R.id.rv_Pending);
            RecyclerView.LayoutManager mLayoutManager = new GridLayoutManager(this, 2);
            rv_Pending.setLayoutManager(mLayoutManager);
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));
            giftRvAdapter = new TeGiftRvAdapter(pending_list, myActivity);
            rv_Pending.setAdapter(giftRvAdapter);
            initScroll_p();
            if (isInternetConnected(myActivity)) {
                get_gift_details("fresh_g");
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void get_gift_details(final String type) {
        if (!type.matches("add_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
//        params.put("te_code", get_TE_code(myActivity));
        params.put("page_no", page_no_P + "");

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_gift_catalog_for_te, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("gift_ ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_P == 1) {
                            tot_count_P = reader.optInt("e_points");
                        }
                        set_TE_points(myActivity, reader.optString("e_points"));
                        page_no_P++;
                        String pending_recommendation_data = reader.getString("gift_data");
                        JSONArray ja = new JSONArray(pending_recommendation_data);
                        if (ja.length() > 0) {
                            if (type.matches("add_p")) {
                                pending_list.remove(p_array_size);
                                giftRvAdapter.notifyItemRemoved(p_array_size);
                            }

                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);

                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem0(e.getString("gift_id")); //gift_id
                                cdh.setItem1(e.getString("gift_title")); //gift_title
                                cdh.setItem2(e.getString("gift_description")); //gift_description
                                cdh.setItem3(e.getString("gift_image_url")); //gift_image_url
                                cdh.setItem4(e.getString("point_require")); //point_require
                                cdh.setItem5(e.getString("point_require_text")); //point_require_text
                                cdh.setItem6(e.optString("button_status")); //button_status
                                cdh.setItem7(reader.optString("e_points")); //e_points

                                pending_list.add(cdh);
                            }
                            if (type.matches("add_p")) {
                                giftRvAdapter.notifyDataSetChanged();
                            } else {
                                giftRvAdapter = new TeGiftRvAdapter(pending_list, myActivity);
                                rv_Pending.setAdapter(giftRvAdapter);
                            }

                            p_array_size = giftRvAdapter.getItemCount_();
//                            tv_points.setText("My Stellar points\n" + reader.optString("e_points"));
                        }
                    } else {
                        page_no_P = -1;
                        if (type.matches("add_p")) {
                            pending_list.remove(p_array_size);
                            giftRvAdapter.notifyItemRemoved(p_array_size);
                        }
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                }
                isLoading_p = false;
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                isLoading_p = false;
            }


        });
    }

    private void initScroll_p() {
        rv_Pending.addOnScrollListener(new RecyclerView.OnScrollListener() {
            @Override
            public void onScrollStateChanged(@NonNull RecyclerView recyclerView, int newState) {
                super.onScrollStateChanged(recyclerView, newState);
            }

            @Override
            public void onScrolled(@NonNull RecyclerView recyclerView, int dx, int dy) {
                super.onScrolled(recyclerView, dx, dy);

                LinearLayoutManager linearLayoutManager = (LinearLayoutManager) recyclerView.getLayoutManager();

                if (!isLoading_p) {
                    if (linearLayoutManager != null && linearLayoutManager.findLastCompletelyVisibleItemPosition() == p_array_size - 1) {
                        //bottom of list!
                        loadMore_p();
                        isLoading_p = true;
                    }
                }
            }
        });
    }

    private void loadMore_p() {
        pending_list.add(null);
        giftRvAdapter.notifyItemInserted(p_array_size);


        Handler handler = new Handler();
        handler.postDelayed(new Runnable() {
            @Override
            public void run() {
                if (isInternetConnected(myActivity)) {
                    if (page_no_P > 0) {
                        get_gift_details("add_p");
                    } else {
                        //Toast.makeText(myActivity, process_message, Toast.LENGTH_SHORT).show();
                        pending_list.remove(p_array_size);
                        giftRvAdapter.notifyItemRemoved(p_array_size);
                        isLoading_p = false;
                        giftRvAdapter.notifyDataSetChanged();
                    }
                } else {
                    Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    pending_list.remove(p_array_size);
                    giftRvAdapter.notifyItemRemoved(p_array_size);
                    isLoading_p = false;
                    giftRvAdapter.notifyDataSetChanged();
                }
            }
        }, 3000);


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

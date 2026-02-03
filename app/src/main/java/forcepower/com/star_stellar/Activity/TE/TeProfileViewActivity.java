package forcepower.com.star_stellar.Activity.TE;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import de.hdodenhof.circleimageview.CircleImageView;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.ProfileListAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_profile_details_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.profile_JSON_data;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_ALL;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_profile_data;

public class TeProfileViewActivity extends BaseActivity {
    Activity myActivity;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();
    CircleImageView iv_Star_Logo_F;
    TextView tv_number_of_sites, tv_number_of_points, tv_number_of_gifts, tv_profiler;
    ListView lv_Profile;
    ProfileListAdapter myAdapter;
    String engineer_id = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile_view);

        try {
            myActivity = TeProfileViewActivity.this;

            RelativeLayout rl_Star_Logo_P = (RelativeLayout) findViewById(R.id.rl_Star_Logo_P);
            rl_Star_Logo_P.getLayoutParams().height = get_Banner_Height(myActivity);

            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);

            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Engineer Details");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });


            tv_profiler = (TextView) findViewById(R.id.tv_profiler);
            tv_number_of_sites = (TextView) findViewById(R.id.tv_number_of_sites);
            tv_number_of_points = (TextView) findViewById(R.id.tv_number_of_points);
            tv_number_of_gifts = (TextView) findViewById(R.id.tv_number_of_gifts);
            lv_Profile = (ListView) findViewById(R.id.lv_Profile);
            myAdapter = new ProfileListAdapter(this, menu_item_list);
            lv_Profile.setAdapter(myAdapter);

            //Profile Photo
            iv_Star_Logo_F = (CircleImageView) findViewById(R.id.iv_Star_Logo_F);
            engineer_id = getIntent().getStringExtra("eid");
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void get_profile_details() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", engineer_id);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_profile_details_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    print_Log_d("profile", str + "");
                    set_profile_data(myActivity, str + "");
                    profile_JSON_data = str;
                    parse_profile_json_data(str);
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

    private void parse_profile_json_data(String str) {
        try {
            final JSONObject reader = new JSONObject(str);
            if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                tv_profiler.setText(reader.optString("e_name"));

                tv_number_of_gifts.setText(reader.optString("number_of_gifts"));
                tv_number_of_points.setText(reader.optString("number_of_points"));
                tv_number_of_sites.setText(reader.optString("number_of_sites"));

                String e_profile_image = reader.optString("e_profile_image");
                Glide.with(myActivity).load(e_profile_image)
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .dontAnimate()
                        .into(iv_Star_Logo_F);

                String profile_data = reader.getString("profile_data");
                JSONArray ja = new JSONArray(profile_data);
                if (ja.length() > 0) {
                    menu_item_list = new ArrayList<>();

                    for (int i = 0; i < ja.length(); i++) {
                        final JSONObject e = ja.getJSONObject(i);

                        CommonHelper cdh = new CommonHelper();
                        cdh.setItem0(e.getString("label"));
                        cdh.setItem1(e.getString("value"));

                        menu_item_list.add(cdh);
                    }
                    myAdapter.setFilter(menu_item_list);
                }

                set_ALL(myActivity,
                        reader.optString("e_dob"),
                        reader.optString("e_dom"),
                        reader.optString("e_address"),
                        reader.optString("e_pin"),
                        reader.optString("e_state"),
                        reader.optString("e_city_town")
                );
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
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

    @Override
    public void onResume() {
        super.onResume();
        if (isInternetConnected(myActivity)) {
            get_profile_details();
        }
    }

    public void ll_sites_recommended(View view) {
        if (isInternetConnected(myActivity)) {
            startActivity(new Intent(myActivity, TeProfileSiteRecommActivity.class));
        } else {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        }
    }

    public void ll_gift_redeem(View view) {
        if (isInternetConnected(myActivity)) {
            startActivity(new Intent(myActivity, TeProfileMyOrdersActivity.class));
        } else {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        }
    }

    public void ll_sites_points(View view) {
        if (isInternetConnected(myActivity)) {
            startActivity(new Intent(myActivity, TeProfileLedgerActivity.class));
        } else {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        }
    }
}

package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.ProgressDialog;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.PendingEngineerAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.branch_zone_list_for_te;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

public class PendingEngineerActivity extends BaseActivity {
    private Activity myActivity;
    private String engineer_data = "";
    private ListView lv_pening_engineer;
    private ArrayList<CommonHelper> engineer_list = new ArrayList<>();
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pending_engineer);

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
            tvCaption.setText("Engineer to be approved");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            engineer_data = getIntent().getStringExtra("engineer_data");
            lv_pening_engineer = (ListView) findViewById(R.id.lv_pening_engineer);

            JSONObject jsonObject = new JSONObject(engineer_data);
            engineer_data = jsonObject.optString("engineer_data");
            JSONArray ja = new JSONArray(engineer_data);
            engineer_list = new ArrayList<>();
            for (int i = 0; i < ja.length(); i++) {
                final JSONObject e = ja.getJSONObject(i);
                CommonHelper commonHelper = new CommonHelper();
                commonHelper.setItem0(e.optString("eid"));
                commonHelper.setItem1(e.optString("e_city_town"));
                commonHelper.setItem2(e.optString("e_mobile"));
                commonHelper.setItem3(e.optString("e_name"));
                commonHelper.setItem4(e.optString("e_profile_image_url"));

                engineer_list.add(commonHelper);
            }
            PendingEngineerAdapter myAdapter = new PendingEngineerAdapter(myActivity, engineer_list);
            lv_pening_engineer.setAdapter(myAdapter);
            lv_pening_engineer.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
//                    TextView tv_hidden_value = (TextView) view.findViewById(R.id.tv_hidden_value);
//                    TextView tv_Menu_item = (TextView) view.findViewById(R.id.tv_Menu_item);
//                    et_Select_product.setText(tv_Menu_item.getText().toString());
//                    expected_product_id = tv_hidden_value.getText().toString(); //prod_id
                }
            });


        } catch (final Exception e) {
            e.printStackTrace();
        } finally {
            get_Branch_List();
        }
    }

    public ArrayList<CommonHelper> returnVal() {
        return pending_list;
    }

    public void get_Branch_List() {

        final RequestParams params = new RequestParams();
        params.put("te_code", get_TE_code(myActivity));

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(branch_zone_list_for_te, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("branch_zone_list_for_te ", str + "");
                    pending_list = new ArrayList<>();
                    final String branch_code = reader.optString("branch_code");
                    final JSONArray ja = new JSONArray(branch_code);
                    if (ja.length() > 0) {

                        for (int i = 0; i < ja.length(); i++) {
                            final JSONObject e = ja.getJSONObject(i);

                            CommonHelper cdh = new CommonHelper();
                            cdh.setItem0(e.getString("br_cod"));
                            cdh.setItem1(e.getString("br_name"));
                            pending_list.add(cdh);

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

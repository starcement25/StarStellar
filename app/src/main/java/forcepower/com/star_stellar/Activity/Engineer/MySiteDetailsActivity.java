package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import org.json.JSONObject;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.Adapter.MySiteDetailsAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class MySiteDetailsActivity extends BaseActivity {
    private Activity myActivity;
    private String json_row = "";
    private ListView lv_mySite_;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();
    private MySiteDetailsAdapter myAdapter;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_my_site_details);

        try {
            myActivity = MySiteDetailsActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("My Site Recommendation");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            json_row = getIntent().getStringExtra("json_row");
            final JSONObject e = new JSONObject(json_row);
            String r_recomended_site_image_url = e.getString("r_recomended_site_image_url");
            print_Log_d("r_recomended_site_image_url", r_recomended_site_image_url);
            ImageView iv_mySite_ = (ImageView) findViewById(R.id.iv_mySite_);
            lv_mySite_ = (ListView) findViewById(R.id.lv_mySite_);
            myAdapter = new MySiteDetailsAdapter(this, menu_item_list);
            lv_mySite_.setAdapter(myAdapter);

            Glide.with(myActivity).load(r_recomended_site_image_url)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .dontAnimate()
                    .placeholder(R.drawable.default_)
                    .error(R.drawable.default_)
                    .into(iv_mySite_);

            parse_mySite(json_row);
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void parse_mySite(String str) {
        try {
            final JSONObject e = new JSONObject(str);
            menu_item_list = new ArrayList<>();

            CommonHelper cdh;

            if (e.has("r_site_name")) {
                cdh = new CommonHelper();
                cdh.setItem0("Site name");
                cdh.setItem1(e.getString("r_site_name"));
                menu_item_list.add(cdh);
            }

            if (e.has("r_address")) {
                cdh = new CommonHelper();
                cdh.setItem0("Site Address");
                cdh.setItem1(e.getString("r_address"));
                menu_item_list.add(cdh);
            }
            if (e.has("r_site_potential_in_mt")) {
                cdh = new CommonHelper();
                cdh.setItem0("Site MT");//replaced potential
                cdh.setItem1(e.getString("r_site_potential_in_mt"));
                menu_item_list.add(cdh);
            }
            if (e.has("r_contact_person_name")) {
                cdh = new CommonHelper();
                cdh.setItem0("Contact Person");
                cdh.setItem1(e.getString("r_contact_person_name"));
                menu_item_list.add(cdh);
            }
            if (e.has("r_contact_person_category_name")) {
                cdh = new CommonHelper();
                cdh.setItem0("Contact Person Category");
                cdh.setItem1(e.getString("r_contact_person_category_name"));
                menu_item_list.add(cdh);
            }
            if (e.has("r_mobile_no")) {
                cdh = new CommonHelper();
                cdh.setItem0("Mobile");
                cdh.setItem1(e.getString("r_mobile_no"));
                menu_item_list.add(cdh);
            }
            if (e.has("actual_consumption")) {
                cdh = new CommonHelper();
                cdh.setItem0("Verified by TE");
                cdh.setItem1(e.getString("actual_consumption") + " bags");
                menu_item_list.add(cdh);
            }

            //
            if (e.has("actual_product_name")) {
                cdh = new CommonHelper();
                cdh.setItem0("Actual Product Name");
                cdh.setItem1(e.getString("actual_product_name"));
                menu_item_list.add(cdh);
            }
            if (e.has("purchased_from")) {
                cdh = new CommonHelper();
                cdh.setItem0("Purchased From");
                cdh.setItem1(e.getString("purchased_from"));
                menu_item_list.add(cdh);
            }
            if (e.has("purchased_from_name")) {
                cdh = new CommonHelper();
                cdh.setItem0("Purchased From Name");
                cdh.setItem1(e.getString("purchased_from_name"));
                menu_item_list.add(cdh);
            }
            if (e.has("purchased_from_area")) {
                cdh = new CommonHelper();
                cdh.setItem0("Purchased From Area");
                cdh.setItem1(e.getString("purchased_from_area"));
                menu_item_list.add(cdh);
            }
            if (e.has("purchased_from_contact_no")) {
                cdh = new CommonHelper();
                cdh.setItem0("Purchased From Contact No");
                cdh.setItem1(e.getString("purchased_from_contact_no"));
                menu_item_list.add(cdh);
            }

            myAdapter.setFilter(menu_item_list);
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }
}

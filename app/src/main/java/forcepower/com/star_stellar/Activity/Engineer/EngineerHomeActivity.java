package forcepower.com.star_stellar.Activity.Engineer;

import android.Manifest;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;

import androidx.appcompat.app.ActionBarDrawerToggle;
import androidx.core.app.ActivityCompat;
import androidx.core.view.GravityCompat;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.viewpager.widget.ViewPager;
import androidx.drawerlayout.widget.DrawerLayout;

import android.os.Build;
import android.os.Bundle;

import androidx.appcompat.app.AlertDialog;

import android.os.Handler;
import android.text.Html;
import android.view.Gravity;
import android.view.View;
import android.view.Window;
import android.widget.AdapterView;
import android.widget.GridView;
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
import java.util.Timer;
import java.util.TimerTask;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.HomeTopPicsAdapter;
import forcepower.com.star_stellar.Activity.LoginStep1Activity;
import forcepower.com.star_stellar.Activity.TermsConditionActivity;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.GridHomeAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.SideMenuListAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.SliderPagerAdapter;
import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.SharedPrefData;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.playStoreUrl;
import static forcepower.com.star_stellar.Class.AllUrl.show_latest_app_version;
import static forcepower.com.star_stellar.Class.AllUrl.ws_home_screen_details_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.hideKeyboard;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.getLoginStatus;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_points;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_firebase_token;
import static forcepower.com.star_stellar.Class.SharedPrefData.setLoginStatus;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_points_msg;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_server_current_date;

public class EngineerHomeActivity extends BaseActivity {
    private DrawerLayout mDrawerLayout;
    private RelativeLayout mLinearLayoutOption;
    private Activity myActivity;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();
    private ArrayList<CommonHelper> grid_item_list = new ArrayList<>();
    private TextView tv_E_name, tv_E_points;

    private ViewPager vp_slider;
    private LinearLayout ll_dots;
    private SliderPagerAdapter sliderPagerAdapter;
    private ArrayList<CommonHelper> slider_image_list = new ArrayList<>();
    private TextView[] dots;
    private int page_position = 0;
    private ImageView iv_redeem_p;
    private RecyclerView rv_top_pics;
    private String update_checked = "No";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_engineer_home);
        try {
            myActivity = EngineerHomeActivity.this;

            mLinearLayoutOption = (RelativeLayout) findViewById(R.id.option_layout);
            mDrawerLayout = (DrawerLayout) findViewById(R.id.drawer_layout);
            new ActionBarDrawerToggle(this, mDrawerLayout,
                    0, 0) {
                //			mDrawerToggle
                public void onDrawerClosed(View view) {
                    super.onDrawerClosed(view);
                }

                public void onDrawerOpened(View drawerView) {
                    super.onDrawerOpened(drawerView);
                }
            };


            //Header_View
            iv_redeem_p = (ImageView) findViewById(R.id.iv_redeem_p);
            final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setImageResource(R.drawable.option_menu);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
                        mDrawerLayout.closeDrawers();
                    } else {
                        hideKeyboard(myActivity);
                        mDrawerLayout.openDrawer(mLinearLayoutOption);
                    }
                }
            });

            //side_menu
//            View side_menu = (View) findViewById(R.id.side_menu);
//            side_menu.getLayoutParams().height = (get_Banner_Height(myActivity)+get_Header_Height(myActivity));

            final ListView lvSideMenu = (ListView) findViewById(R.id.lvSideMenu);
            final String[] item_side_menu = {"About Star Cement", "Profile", "Stellar Points", "My Orders",
                    "Notification", "New Order Query", "Terms and Condition", "Contact Us", "Log out"};
            menu_item_list = new ArrayList<>();
            for (int i = 0; i < item_side_menu.length; i++) {
                final CommonHelper cdh = new CommonHelper();
                cdh.setItem0(item_side_menu[i]);

                menu_item_list.add(cdh);
            }

            final SideMenuListAdapter myAdapter = new SideMenuListAdapter(this, menu_item_list);
            lvSideMenu.setAdapter(myAdapter);
            lvSideMenu.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                    if (isInternetConnected(myActivity)) {
                        TextView tv_Menu_item = view.findViewById(R.id.tv_Menu_item);
                        final String menu_item = tv_Menu_item.getText().toString() + "";
                        set_redirect(menu_item);
                        if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
                            mDrawerLayout.closeDrawers();
                        }
                    } else {
                        msg_Dialog(checkInternetConnection);
                    }
                }
            });

            //Grid
            final int[] imgArray = new int[]{R.drawable.addsite, R.drawable.site, R.drawable.gifts};
            final String[] menu_top = new String[]{"New Site", "Recommended", "Gift Catalogue &",};
            final String[] menu_bottom = new String[]{"Recommendation", "Site Status", "Redemption"};
            menu_item_list = new ArrayList<>();

            grid_item_list = new ArrayList<>();
            for (int i = 0; i < imgArray.length; i++) {
                final CommonHelper cdh = new CommonHelper();
                cdh.setintValue0(imgArray[i]);
                cdh.setItem1(menu_top[i]);
                cdh.setItem2(menu_bottom[i]);

                grid_item_list.add(cdh);
            }
            final GridView mGridViewMenu = (GridView) findViewById(R.id.gv_home_menu);
            final GridHomeAdapter mMenuAdapter = new GridHomeAdapter(myActivity, grid_item_list);
            mGridViewMenu.setAdapter(mMenuAdapter);
            mGridViewMenu.setOnItemClickListener(new AdapterView.OnItemClickListener() {
                @Override
                public void onItemClick(AdapterView<?> adapterView, View view, int pos, long l) {
//                    TextView tv_featureName = (TextView) view.findViewById(R.id.tv_featureName_T);
                    if (isInternetConnected(myActivity)) {
                        set_redirect(menu_top[pos]);
                    } else {
                        msg_Dialog(checkInternetConnection);
                    }
                }
            });
            //Welcome
            tv_E_name = (TextView) findViewById(R.id.tv_E_name);
            tv_E_points = (TextView) findViewById(R.id.tv_E_points);

            //SLIDER
            // method for initialisation
            init();

            final Handler handler = new Handler();

            final Runnable update = new Runnable() {
                public void run() {
                    if (page_position == slider_image_list.size()) {
                        page_position = 0;
                    } else {
                        page_position = page_position + 1;
                    }
                    vp_slider.setCurrentItem(page_position, true);
                }
            };

            new Timer().schedule(new TimerTask() {

                @Override
                public void run() {
                    handler.post(update);
                }
            }, 100, 5000);

            //version_check
            final LinearLayout ll_stellar_points = (LinearLayout) findViewById(R.id.ll_stellar_points);
            ll_stellar_points.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    set_redirect("Stellar Points");
                }
            });

            //
            rv_top_pics = (RecyclerView) findViewById(R.id.rv_top_pics);

            if (getIntent().hasExtra("process_message")) {
                msg_Dialog(getIntent().getStringExtra("process_message"));
            }

            //
            final TextView tv_app_version = (TextView) findViewById(R.id.tv_app_version);
            tv_app_version.setText("App version- " + BuildConfig.VERSION_NAME);
            final RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
            rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));
            rl_Star_Logo.setBackgroundResource(R.drawable.star_logo_square);
        } catch (final Exception e) {
            e.printStackTrace();
        } finally {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
                if (ActivityCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                    requestPermissions(new String[]{Manifest.permission.POST_NOTIFICATIONS}, 1100);
                }
            }
        }
    }

    private ArrayList<CommonHelper> top_pics_list = new ArrayList<>();
    private HomeTopPicsAdapter gAdapter;

    private void msg_Dialog(String msg) {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");

            builder.setPositiveButton("Ok", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void exit_Dialog() {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage("Do you want to exit?");

            builder.setNegativeButton("No", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    finishAffinity();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void logOut() {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage("Do you want to log out?");

            builder.setNegativeButton("No", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    setLoginStatus(myActivity, false);
                    startActivity(new Intent(myActivity, LoginStep1Activity.class));
                    finishAffinity();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void auto_logOut(final String msg) {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");

            builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                    setLoginStatus(myActivity, false);
                    startActivity(new Intent(myActivity, LoginStep1Activity.class));
                    finishAffinity();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(false);
            dialog.setCanceledOnTouchOutside(false);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void contactUs() {
        try {
            final CharSequence[] items = {"Email", "Phone", "WhatsApp"};

            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText("Choose an option for contact with us");
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            builder.setCustomTitle(tvCPopup);

            builder.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int item) {

                    if (items[item].equals("Phone")) {
                        Intent intent = new Intent(Intent.ACTION_DIAL);
                        intent.setData(Uri.parse("tel:180034534500"));
                        startActivity(intent);
                    } else if (items[item].equals("Email")) {
                        Intent intent = new Intent(Intent.ACTION_SEND);
                        intent.setType("text/html");
                        intent.putExtra(Intent.EXTRA_EMAIL, new String[]{"starstellar@starcement.co.in"});
                        intent.putExtra(Intent.EXTRA_SUBJECT, getResources().getString(R.string.app_name));
                        intent.putExtra(Intent.EXTRA_TEXT, "I'm using " + getResources().getString(R.string.app_name));

                        startActivity(Intent.createChooser(intent, "Send Email"));
                    } else if (items[item].equals("WhatsApp")) {
                        String mSendToWhatsApp = "https://wa.me/+917595080005" + "?text=Hi, \n ";
                        startActivity(new Intent(Intent.ACTION_VIEW,
                                Uri.parse(
                                        mSendToWhatsApp
                                )));
                    }
                }
            }).setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.show();
            builder.setCancelable(true);
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void continueAppVersionCheck() {
        try {
            loadDialog();

            final RequestParams params = new RequestParams();
            params.put("the_engineer_id", get_E_id(myActivity));
            params.put("en_registration_id", get_firebase_token(myActivity));
            params.put("en_device_id", SharedPrefData.getDeviceId(myActivity));
            params.put("en_device_type", "ANDROID");
            params.put("app_version", BuildConfig.VERSION_NAME);

//the_engineer_id,en_device_id,en_registration_id,en_device_type
            print_Log_d("dhr44er_U ", show_latest_app_version + "");
            print_Log_d("dhr44er_P ", params + "");
            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);
            client.post(show_latest_app_version, params, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    final String str = new String(responseBody);
                    try {

                        print_Log_d("dhr44er_R ", str + "");

                        final JSONObject reader = new JSONObject(str);
                        update_checked = "Yes";
                        set_TE_name(myActivity, reader.optString("te_name"));
                        set_TE_code(myActivity, reader.optString("te_code"));
                        print_Log_d("curr_dhr44er_b ", reader.optString("current_date"));
                        print_Log_d("curr_dhr44er_a ", reader.optString("current_date").replaceAll("\\s.*", ""));
                        set_server_current_date(myActivity, reader.optString("current_date").replaceAll("\\s.*", ""));
                        if (reader.optString("engineer_force_logout").equalsIgnoreCase("YES")) {
                            setLoginStatus(myActivity, false);
                            auto_logOut(reader.optString("force_logout_message"));
                        } else if (reader.optDouble("android_app_version") > Double.parseDouble(BuildConfig.VERSION_NAME)) {
                            app_version_Dialog(reader.getString("process_message"));
                        } else {
                            getHomeScreenContent();
                        }
                    } catch (final Exception e) {
                        e.printStackTrace();
                    } finally {
                        dismissDialog();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    print_Log_d("dhr44er_Err ", error.toString());

                    dismissDialog();
                }


            });
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void app_version_Dialog(String msg) {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");
            builder.setPositiveButton("Update", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    startActivity(new Intent(Intent.ACTION_VIEW, Uri.parse(playStoreUrl + getPackageName())));
                    finishAffinity();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(false);
            dialog.setCanceledOnTouchOutside(false);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
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

    @Override
    public void onResume() {
        super.onResume();
        try {
            if (getLoginStatus(myActivity)) {
                open_home_refresh(null);
            } else {
                startActivity(new Intent(myActivity, LoginStep1Activity.class));
                finishAffinity();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void init() {
        try {
            vp_slider = (ViewPager) findViewById(R.id.vp_slider);
            ll_dots = (LinearLayout) findViewById(R.id.ll_dots);

            slider_image_list = new ArrayList<>();
            sliderPagerAdapter = new SliderPagerAdapter(myActivity, slider_image_list);
            vp_slider.setAdapter(sliderPagerAdapter);

            vp_slider.addOnPageChangeListener(new ViewPager.OnPageChangeListener() {
                @Override
                public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

                }

                @Override
                public void onPageSelected(int position) {
                    page_position = position;
                    addBottomDots(page_position);
                }

                @Override
                public void onPageScrollStateChanged(int state) {

                }
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void addBottomDots(int currentPage) {
        try {
            dots = new TextView[slider_image_list.size()];

            ll_dots.removeAllViews();
            for (int i = 0; i < dots.length; i++) {
                dots[i] = new TextView(this);
                dots[i].setText(Html.fromHtml("&#8226;"));
                dots[i].setTextSize(22);
                dots[i].setTextColor(getResources().getColor(R.color.colorBlack));
                dots[i].setGravity(Gravity.CENTER);
                ll_dots.addView(dots[i]);
            }

            if (dots.length > 0) {
                dots[currentPage].setTextColor(getResources().getColor(R.color.red));
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        if (mDrawerLayout.isDrawerOpen(GravityCompat.START)) {
            mDrawerLayout.closeDrawers();
        } else {
            exit_Dialog();
        }
    }

    public void getHomeScreenContent() {
        try {
            loadDialog();

            final RequestParams params = new RequestParams();
            params.put("the_engineer_id", get_E_id(myActivity));

            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);
            client.post(ws_home_screen_details_for_engineer, params, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    final String str = new String(responseBody);
                    try {
                        final JSONObject reader = new JSONObject(str);
                        print_Log_d("get_Home_screen_data", str + "");

                        if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                            Glide.with(myActivity).load(reader.optString("top_section_image_link"))
                                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                                    .skipMemoryCache(true)
                                    .dontAnimate()
                                    .placeholder(R.drawable.sample_man_square)
                                    .error(R.drawable.sample_man_square)
                                    .into(iv_redeem_p);

                            tv_E_name.setText("Hi, \n" + reader.optString("e_name"));
                            set_E_name(myActivity, reader.optString("e_name"));
                            TextView tv_top_section_header_text = (TextView) findViewById(R.id.tv_top_section_header_text);
                            tv_top_section_header_text.setText(reader.optString("top_section_header_text"));

                            TextView top_section_description_text = (TextView) findViewById(R.id.tv_redeem_d);
                            top_section_description_text.setText(reader.optString("top_section_description_text"));

                            set_E_points_msg(myActivity, reader.optString("number_of_points"));
                            tv_E_points.setText(reader.optString("number_of_points"));

                            String offer_slider_data = reader.optString("offer_slider_data");
                            JSONArray ja = new JSONArray(offer_slider_data);

                            slider_image_list = new ArrayList<>();
                            for (int i = 0; i < ja.length(); i++) {
                                final JSONObject e = ja.getJSONObject(i);
                                String slider_description_text = e.optString("slider_description_text") + "";
                                String slider_header_text = e.optString("slider_header_text") + "";
                                String slider_image_link = e.optString("slider_image_link") + "";

                                CommonHelper commonHelper = new CommonHelper();
                                commonHelper.setItem0(slider_description_text);
                                commonHelper.setItem1(slider_header_text);
                                commonHelper.setItem2(slider_image_link);
                                commonHelper.setItem3(e.optString("slider_category"));

                                slider_image_list.add(commonHelper);
                            }

                            // method for adding indicators
                            page_position = 0;
                            addBottomDots(page_position);
                            sliderPagerAdapter = new SliderPagerAdapter(myActivity, slider_image_list);
                            vp_slider.setAdapter(sliderPagerAdapter);


                            //Top pics
                            String featured_slider_data = reader.optString("featured_slider_data");
                            JSONArray ja_2 = new JSONArray(featured_slider_data);

                            top_pics_list = new ArrayList<>();
                            for (int i = 0; i < ja_2.length(); i++) {
                                final JSONObject e = ja_2.getJSONObject(i);
                                String featured_gift_id = e.optString("featured_gift_id") + "";
                                String featured_gift_title = e.optString("featured_gift_title") + "";
                                String featured_gift_image_link = e.optString("featured_gift_image_link") + "";


                                CommonHelper cdh = new CommonHelper();
                                cdh.setItem1(featured_gift_title); //gift_title
                                cdh.setItem3(featured_gift_image_link); //gift_image_url

                                top_pics_list.add(cdh);
                            }

                            rv_top_pics.setLayoutManager(new LinearLayoutManager(myActivity, LinearLayoutManager.HORIZONTAL, false));

                            gAdapter = new HomeTopPicsAdapter(top_pics_list, myActivity);
                            rv_top_pics.setAdapter(gAdapter);

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
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void open_home_noti(View v) {
        try {
            set_redirect("Notification");
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void set_redirect(final String menu_item) {
        try {
            if (menu_item.equalsIgnoreCase("Log out")) {
                logOut();
            } else if (update_checked.equalsIgnoreCase("Yes")) {
                if (isInternetConnected(myActivity)) {
                    if (menu_item.equalsIgnoreCase("New Site")) {
                        startActivity(new Intent(myActivity, NewSiteActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Recommended")) {
                        startActivity(new Intent(myActivity, MySiteActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Gift Catalogue &")) {
                        startActivity(new Intent(myActivity, EngGiftsActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Stellar Points")) {
                        startActivity(new Intent(myActivity, EngineerLedgerActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Profile")) {
                        startActivity(new Intent(myActivity, EngineerProfileViewActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Notification")) {
                        startActivity(new Intent(myActivity, NotificationsActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Terms and Condition")) {
                        startActivity(new Intent(myActivity, TermsConditionActivity.class));
                    } else if (menu_item.equalsIgnoreCase("New Order Query")) {
                        startActivity(new Intent(myActivity, EngineerPlaceOrderActivity.class));
                    } else if (menu_item.equalsIgnoreCase("Contact Us")) {
                        contactUs();
                    } else if (menu_item.equalsIgnoreCase("My Orders")) {
                        startActivity(new Intent(myActivity, MyOrdersActivity.class));
                    } else if (menu_item.equalsIgnoreCase("About Star Cement")) {
                        startActivity(new Intent(myActivity, AboutStarCementActivity.class));
                    }
                } else {
                    msg_Dialog(checkInternetConnection);
                }
            } else {
                if (isInternetConnected(myActivity)) {
                    continueAppVersionCheck();
                } else {
                    msg_Dialog(checkInternetConnection);
                }
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void open_home_refresh(View v) {
        try {
            if (isInternetConnected(myActivity)) {
                continueAppVersionCheck();
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                tv_E_name.setText("Hi, \n" + get_E_name(myActivity));
                tv_E_points.setText(get_E_points(myActivity));
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

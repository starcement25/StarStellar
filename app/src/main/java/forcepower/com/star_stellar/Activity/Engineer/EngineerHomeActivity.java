package forcepower.com.star_stellar.Activity.Engineer;

import android.Manifest;
import android.app.Activity;
import android.app.DatePickerDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.graphics.drawable.ColorDrawable;
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
import android.os.Looper;
import android.text.Html;
import android.text.SpannableString;
import android.text.Spanned;
import android.text.TextPaint;
import android.text.method.LinkMovementMethod;
import android.text.style.ClickableSpan;
import android.util.DisplayMetrics;
import android.util.Log;
import android.util.TypedValue;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.webkit.WebView;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.bumptech.glide.load.resource.bitmap.RoundedCorners;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.forcepower.starcement.custom.AsyncTaskCoroutine;
import org.json.JSONArray;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Calendar;
import java.util.Objects;
import java.util.Timer;
import java.util.TimerTask;

import cz.msebera.android.httpclient.Header;
import cz.msebera.android.httpclient.NameValuePair;
import cz.msebera.android.httpclient.message.BasicNameValuePair;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.HomeTopPicsAdapter;
import forcepower.com.star_stellar.Activity.GiftCategoryActivity;
import forcepower.com.star_stellar.Activity.LoginStep1Activity;
import forcepower.com.star_stellar.Activity.TermsConditionActivity;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.GridHomeAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.SideMenuListAdapter;
import forcepower.com.star_stellar.Activity.Engineer.Adapter.SliderPagerAdapter;
import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.HTTPUtils;
import forcepower.com.star_stellar.Class.SharedPrefData;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.api_save_feedback;
import static forcepower.com.star_stellar.Class.AllUrl.engineer_birthday_wish_seen;
import static forcepower.com.star_stellar.Class.AllUrl.get_engineer_dob;
import static forcepower.com.star_stellar.Class.AllUrl.last_order_delivered_api;
import static forcepower.com.star_stellar.Class.AllUrl.store_token;
import static forcepower.com.star_stellar.Class.AllUrl.terms_api;
import static forcepower.com.star_stellar.Class.AllUrl.update_engineer_dob;
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
import static forcepower.com.star_stellar.Class.SharedPrefData.get_TE_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_firebase_token;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_user_type;
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
    //private RecyclerView rv_top_pics;
    private String update_checked = "No", termsAndConditions;

    //--------------------terms and condition popup--------------------
    private LinearLayout reward_t_and_c_popup_layout;
    private TextView reward_t_and_c_popup_text,popup_check_box_text;
    private ImageView popup_check_box;
    private Button popup_t_c_submit_button;
    private int tc_popup_value=0;
    private WebView webView;
    private Boolean isBirthdayDialogShown;
    //--------------------terms and condition popup--------------------

    //--------------------Sorry for Inconvenience popup--------------------
    private LinearLayout not_now_popup_layout;
    private TextView notNow_text;
    private Button popup_not_now_button;
    //--------------------Sorry for Inconvenience popup--------------------

    //--------------------Acknowledment Warning popup--------------------
    private LinearLayout ack_warning_popup_layout;
    private TextView ackWarning_text;
    private Button popup_ack_warning_button;
    private RelativeLayout birthday_popup_layout;
    private RelativeLayout birthday_card_container;
    private ImageView birthday_background_image;
    private ImageView birthday_popup_close;
    private TextView birthday_title;
    private TextView birthday_user_name;
    private TextView birthday_message;
    //--------------------Acknowledment Warning popup--------------------

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_engineer_home);
        try {
            myActivity = EngineerHomeActivity.this;
            tc_popup_value=0;

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
            //tv_E_points = (TextView) findViewById(R.id.tv_E_points);
            tv_E_points = (TextView) findViewById(R.id.tv_balance_value);

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
            //rv_top_pics = (RecyclerView) findViewById(R.id.rv_top_pics);

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
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
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
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
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
    public void storeTokenToServer() {

        final RequestParams params = new RequestParams();
        params.put("user_id", get_E_id(myActivity));
        params.put("user_type", "ENGINEER");
        params.put("token",get_firebase_token(myActivity));
        print_Log_d("storeTokenToServer", get_firebase_token(myActivity));

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);

        client.post(store_token, params, new AsyncHttpResponseHandler() {

            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                dismissDialog();
                try {
                    String response = new String(responseBody);
                    print_Log_d("storeTokenToServer", response);

                    JSONObject json = new JSONObject(response);
                    if (json.optString("status").equalsIgnoreCase("success")) {
                        //Toast.makeText(myActivity, "Token stored successfully", Toast.LENGTH_SHORT).show();
                    } else {
                        //Toast.makeText(myActivity, "Failed to store token: " + json.optString("process_message"), Toast.LENGTH_SHORT).show();
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    //Toast.makeText(myActivity, "Response parsing error", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                Toast.makeText(myActivity, "API call failed: " + error.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void logOut() {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
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
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
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

            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);

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

    private void setTermsWithReadMore(String link) {
        try {
            String readMoreText = "Read more";
            String fullText = termsAndConditions + " " + readMoreText;

            SpannableString spannableString = new SpannableString(fullText);

            // Make "Read more" clickable
            ClickableSpan clickableSpan = new ClickableSpan() {
                @Override
                public void onClick(View widget) {
                    //String readMoreUrl = "https://dev.starstellar.com/terms-and-conditions"; // Change to your URL
                    Intent browserIntent = new Intent(Intent.ACTION_VIEW, Uri.parse(link));
                    startActivity(browserIntent);
                }

                @Override
                public void updateDrawState(TextPaint ds) {
                    super.updateDrawState(ds);
                    ds.setColor(getResources().getColor(R.color.red)); // Red color
                    ds.setUnderlineText(true); // Underline
                }
            };

            // Apply clickable span to "Read more" text only
            int startIndex = fullText.indexOf(readMoreText);
            int endIndex = startIndex + readMoreText.length();
            spannableString.setSpan(clickableSpan, startIndex, endIndex, Spanned.SPAN_EXCLUSIVE_EXCLUSIVE);

           // reward_t_and_c_popup_text.setText(spannableString);
           // reward_t_and_c_popup_text.setMovementMethod(LinkMovementMethod.getInstance());

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void app_version_Dialog(String msg) {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
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
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
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
            //fetchTermsAndConditions();
            checkBirthdayStatus();
            storeTokenToServer();
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


            //--------------------terms and condition popup--------------------

            //--------------------terms and condition popup--------------------

            //--------------------Sorry for Inconvenience popup--------------------
            not_now_popup_layout=findViewById(R.id.not_now_popup_layout);
            notNow_text=findViewById(R.id.notNow_text);
            popup_not_now_button=findViewById(R.id.popup_not_now_button);

            not_now_popup_layout.setVisibility(View.GONE);

            popup_not_now_button.setOnClickListener(v -> {
                not_now_popup_layout.setVisibility(View.GONE);
            });
            //--------------------Sorry for Inconvenience popup--------------------

            //--------------------Acknowledment Warning popup--------------------
            ack_warning_popup_layout=findViewById(R.id.ack_warning_popup_layout);
            ackWarning_text=findViewById(R.id.ackWarning_text);
            popup_ack_warning_button=findViewById(R.id.popup_ack_warning_button);

            popup_ack_warning_button.setOnClickListener(v -> {
                Intent intent = new Intent(myActivity, MyOrdersActivity.class);
                intent.putExtra("openDelivered", true);
                startActivity(intent);
                ack_warning_popup_layout.setVisibility(View.GONE);
            });
            ack_warning_popup_layout.setOnClickListener(v->{
                ack_warning_popup_layout.setVisibility(View.GONE);
            });

            checkPendingAcknowledgment();
            //--------------------Acknowledment Warning popup--------------------

            birthday_popup_layout = findViewById(R.id.birthday_popup_layout);
            birthday_card_container = findViewById(R.id.birthday_card_container);
            birthday_background_image = findViewById(R.id.birthday_background_image);
            birthday_popup_close = findViewById(R.id.birthday_popup_close);
            birthday_title = findViewById(R.id.birthday_title);
            birthday_user_name = findViewById(R.id.birthday_user_name);
            birthday_message = findViewById(R.id.birthday_message);

            birthday_popup_layout.setVisibility(View.GONE);

// Close button click listener
            birthday_popup_close.setOnClickListener(v -> closeBirthdayPopup());

// Background click to close
            birthday_popup_layout.setOnClickListener(v -> closeBirthdayPopup());

// Prevent card container clicks from closing popup
            birthday_card_container.setOnClickListener(v -> {
                // Do nothing - just consume the click
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    //Api Call for Acknowledgment
    public void checkPendingAcknowledgment() {
        //ack_warning_popup_layout.setVisibility(View.VISIBLE);
        final RequestParams params = new RequestParams();
        params.put("engineer_id", get_E_id(myActivity));
        Log.d("params_", params.toString());

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);

        client.post(last_order_delivered_api, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String strResponse = new String(responseBody);
                try {
                    JSONObject reader = new JSONObject(strResponse);
                    String msg = reader.optString("message");
                    String status = reader.optString("status");
                    //Toast.makeText(myActivity, msg, Toast.LENGTH_SHORT).show();
                    if(status.equals("success")){
                        ack_warning_popup_layout.setVisibility(View.VISIBLE);
                    }else{
                        ack_warning_popup_layout.setVisibility(View.GONE);
                    }

                } catch (Exception e) {
                    e.printStackTrace();
                    Toast.makeText(myActivity, "Response parse error", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                Toast.makeText(myActivity, "Failed to submit feedback", Toast.LENGTH_SHORT).show();
            }
        });
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
                            tv_E_points.setText(reader.optString("number_of_points").replace("Stellar Points :", "").trim());
                            Log.d("points=====", reader.optString("number_of_points"));

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

                            //rv_top_pics.setLayoutManager(new LinearLayoutManager(myActivity, LinearLayoutManager.HORIZONTAL, false));

                            gAdapter = new HomeTopPicsAdapter(top_pics_list, myActivity);
                            //rv_top_pics.setAdapter(gAdapter);

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
                        //startActivity(new Intent(myActivity, EngGiftsActivity.class));
                        startActivity(new Intent(myActivity, GiftCategoryActivity.class));
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
                tv_E_points.setText(get_E_points(myActivity).replace("Stellar Points :", "").trim());
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
    private void checkBirthdayStatus() {
        if (HTTPUtils.isConnectionPossible(myActivity)) {
            new TRANS_GetBirthdayStatus_Asynctask(myActivity).execute();
        }
    }

    private void showBirthdayPopup(BirthdayResponse response) {
        try {
            // Set data to views
            birthday_title.setText(response.getTitle());
            birthday_user_name.setText(response.getCustomer_name());
            birthday_message.setText(response.getMessage());
            int radius = (int) TypedValue.applyDimension(
                    TypedValue.COMPLEX_UNIT_DIP,
                    16,
                    getResources().getDisplayMetrics()
            );

            Glide.with(myActivity)
                    .load(response.getImg())
                    .diskCacheStrategy(DiskCacheStrategy.ALL)
                    .placeholder(R.drawable.wish_backgroud)
                    .transform(new RoundedCorners(radius))
                    .error(R.drawable.wish_backgroud)
                    .into(birthday_background_image);

            // Show popup with animation
            birthday_popup_layout.setVisibility(View.VISIBLE);
            Animation popupEnter = AnimationUtils.loadAnimation(myActivity, R.anim.popup_enter);
            birthday_card_container.startAnimation(popupEnter);

            // Add celebratory bounce animation after entrance
            popupEnter.setAnimationListener(new Animation.AnimationListener() {
                @Override
                public void onAnimationStart(Animation animation) {}

                @Override
                public void onAnimationEnd(Animation animation) {
                    Animation bounceCelebrate = AnimationUtils.loadAnimation(myActivity, R.anim.shake);
                    birthday_card_container.startAnimation(bounceCelebrate);
                }

                @Override
                public void onAnimationRepeat(Animation animation) {}
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    private void closeBirthdayPopup() {
        try {
            // Call API to mark birthday as seen
            String customerId = get_E_id(myActivity);
            if (customerId != null && !customerId.isEmpty()) {
                new TRANS_MarkBirthdayAsSeen_Asynctask(myActivity, customerId).execute();
            }

            Animation popupExit = AnimationUtils.loadAnimation(myActivity, R.anim.popup_exit);
            birthday_card_container.startAnimation(popupExit);

            popupExit.setAnimationListener(new Animation.AnimationListener() {
                @Override
                public void onAnimationStart(Animation animation) {}

                @Override
                public void onAnimationEnd(Animation animation) {
                    birthday_popup_layout.setVisibility(View.GONE);
                }

                @Override
                public void onAnimationRepeat(Animation animation) {}
            });
        } catch (Exception e) {
            e.printStackTrace();
            birthday_popup_layout.setVisibility(View.GONE);
        }
    }
    // Birthday API AsyncTask
    public final class TRANS_GetBirthdayStatus_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private BirthdayResponse birthdayResponse;

        public TRANS_GetBirthdayStatus_Asynctask(final Activity mContext) {
            this.mContext = mContext;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        public String doInBackground(final String... params) {
            String GET_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        String customerId = get_E_id(mContext);
                        final String url = get_engineer_dob + "?eid=" + customerId + "&user_type=" + get_user_type(mContext);

                        print_Log_d("BIRTHDAY_API_URL", url);

                        GET_result = HTTPUtils.getDataByHTTP_GET(mContext, url);

                        print_Log_d("BIRTHDAY_API_RESPONSE", GET_result);

                        if (GET_result != null && !GET_result.isEmpty()) {
                            JSONObject jo = new JSONObject(GET_result);
                            birthdayResponse = new BirthdayResponse();
                            birthdayResponse.setStatus(jo.optBoolean("status", false));
                            birthdayResponse.setCustomer_id(jo.optString("customer_id"));
                            birthdayResponse.setCustomer_name(jo.optString("customer_name"));
                            birthdayResponse.setType(jo.optString("type"));
                            birthdayResponse.setTitle(jo.optString("title"));
                            birthdayResponse.setSms(jo.optString("sms"));
                            birthdayResponse.setMessage(jo.optString("message"));
                            birthdayResponse.setDob(jo.optString("dob"));
                            birthdayResponse.setImg(jo.optString("img"));
                        }
                    } catch (Exception e) {
                        print_Log_d("BIRTHDAY_API_ERROR", e.toString());
                        GET_result = "Network Failure";
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return GET_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                if (birthdayResponse != null && birthdayResponse.isStatus()) {
                    new Handler(Looper.getMainLooper()).postDelayed(() -> showBirthdayPopup(birthdayResponse), 4000);
                } else if(birthdayResponse != null && Objects.equals(birthdayResponse.getMessage(), "Birthday Not Found")) {
                    showBirthdayDialog();
                } else {
                    isBirthdayDialogShown = false;
                }
            } catch (Exception e) {
                e.printStackTrace();
                isBirthdayDialogShown = false;
            }
        }
    }

    public final class TRANS_MarkBirthdayAsSeen_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private String customerId;

        public TRANS_MarkBirthdayAsSeen_Asynctask(final Activity mContext, String customerId) {
            this.mContext = mContext;
            this.customerId = customerId;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = engineer_birthday_wish_seen;

                        ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(1);
                        nameValuePairs.add(new BasicNameValuePair("eid", customerId));
                        nameValuePairs.add(new BasicNameValuePair("user_type", customerId));


                        print_Log_d("BIRTHDAY_SEEN_URL", url);
                        print_Log_d("BIRTHDAY_SEEN_PARAMS", nameValuePairs.toString());

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                        print_Log_d("BIRTHDAY_SEEN_RESPONSE", POST_result);

                    } catch (Exception e) {
                        print_Log_d("BIRTHDAY_SEEN_ERROR", e.toString());
                        POST_result = "Network Failure";
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            try {
                if (result != null && !result.isEmpty() && !result.equals("Network Failure")) {
                    JSONObject jo = new JSONObject(result);
                    print_Log_d("BIRTHDAY_SEEN_STATUS", jo.toString());
                    // Handle response if needed
                    if (jo.has("status")) {
                        print_Log_d("BIRTHDAY_SEEN_SUCCESS", "Status: " + jo.optString("status"));
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    // Inner class for Birthday Response
    public static class BirthdayResponse {
        private boolean status;
        private String customer_id;
        private String customer_name;
        private String type;
        private String title;
        private String sms;
        private String message;
        private String dob;
        private String img;

        public boolean isStatus() { return status; }
        public void setStatus(boolean status) { this.status = status; }
        public String getCustomer_id() { return customer_id; }
        public void setCustomer_id(String customer_id) { this.customer_id = customer_id; }
        public String getCustomer_name() { return customer_name; }
        public void setCustomer_name(String customer_name) { this.customer_name = customer_name; }
        public String getType() { return type; }

        public String getImg() {
            return img;
        }

        public void setImg(String img) {
            this.img = img;
        }

        public String getTitle() {
            return title;
        }

        public void setTitle(String title) {
            this.title = title;
        }

        public void setType(String type) { this.type = type; }
        public String getSms() { return sms; }
        public void setSms(String sms) { this.sms = sms; }
        public String getMessage() { return message; }
        public void setMessage(String message) { this.message = message; }
        public String getDob() { return dob; }
        public void setDob(String dob) { this.dob = dob; }
    }

    private void showBirthdayDialog() {
        final Dialog dialog = new Dialog(this);
        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
        dialog.setContentView(R.layout.dialog_birthday_picker);
        dialog.setCancelable(false);
        dialog.setCanceledOnTouchOutside(false);

        if (dialog.getWindow() != null) {
            dialog.getWindow().setBackgroundDrawable(new ColorDrawable(Color.TRANSPARENT));

            DisplayMetrics displayMetrics = new DisplayMetrics();
            getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
            int displayWidth = displayMetrics.widthPixels;

            int margin = (int) (32 * getResources().getDisplayMetrics().density);

            WindowManager.LayoutParams layoutParams = new WindowManager.LayoutParams();
            layoutParams.copyFrom(dialog.getWindow().getAttributes());
            layoutParams.width = displayWidth - (margin * 2);
            layoutParams.height = WindowManager.LayoutParams.WRAP_CONTENT;
            dialog.getWindow().setAttributes(layoutParams);
        }

        final TextView tvBirthdate = dialog.findViewById(R.id.tvBirthdate);
        final LinearLayout dateInputContainer = dialog.findViewById(R.id.dateInputContainer);
        Button btnCancel = dialog.findViewById(R.id.btnCancel);
        btnCancel.setVisibility(View.GONE);
        final Button btnConfirm = dialog.findViewById(R.id.btnConfirm);

        dateInputContainer.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                showDatePicker(tvBirthdate, btnConfirm);
            }
        });

        btnCancel.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                isBirthdayDialogShown = false; // Reset flag when dialog is cancelled
                dialog.dismiss();
            }
        });

        btnConfirm.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String selectedDate = tvBirthdate.getText().toString();
                if (!selectedDate.equals("Select Date")) {
                    String formattedDate = selectedDate.replace("/", "-");
                    updateCustomerDOB(formattedDate);
                    dialog.dismiss();
                } else {
                    Toast.makeText(myActivity, "Please select a date", Toast.LENGTH_SHORT).show();
                }
            }
        });

        dialog.setOnDismissListener(new DialogInterface.OnDismissListener() {
            @Override
            public void onDismiss(DialogInterface dialogInterface) {
                // Only reset if date was actually selected and submitted
                // Flag will be reset by the birthday check after update
            }
        });

        dialog.show();
    }


    private void showDatePicker(final TextView dateTextView, final Button confirmButton) {
        final Calendar calendar = Calendar.getInstance();
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                new DatePickerDialog.OnDateSetListener() {
                    @Override
                    public void onDateSet(DatePicker view, int selectedYear, int selectedMonth, int selectedDay) {
                        String birthday = String.format("%02d/%02d/%d",
                                selectedDay, (selectedMonth + 1), selectedYear);

                        dateTextView.setText(birthday);
                        dateTextView.setTextColor(0xFF1A1A1A);

                        confirmButton.setEnabled(true);
                    }
                },
                year,
                month,
                day
        );

        datePickerDialog.setCancelable(false);
        datePickerDialog.setCanceledOnTouchOutside(false);
        datePickerDialog.getDatePicker().setMaxDate(System.currentTimeMillis());

        Calendar minDate = Calendar.getInstance();
        minDate.set(Calendar.YEAR, year - 120);
        datePickerDialog.getDatePicker().setMinDate(minDate.getTimeInMillis());

        datePickerDialog.show();
    }
    public void updateCustomerDOB(String dob) {
        if (HTTPUtils.isConnectionPossible(myActivity)) {
            new TRANS_UpdateCustomerDOB_Asynctask(myActivity, dob).execute();
        } else {
            Toast.makeText(myActivity, "Something Went Wrong. Please check your internet connection.", Toast.LENGTH_SHORT).show();
        }
    }

    public final class TRANS_UpdateCustomerDOB_Asynctask extends AsyncTaskCoroutine<String, String> {
        private Activity mContext;
        private String dob;

        public TRANS_UpdateCustomerDOB_Asynctask(final Activity mContext, String dob) {
            this.mContext = mContext;
            this.dob = dob;
        }

        @Override
        public void onPreExecute() {
            super.onPreExecute();
            loadDialog();

        }

        @Override
        public String doInBackground(final String... params) {
            String POST_result = "";
            try {
                if (HTTPUtils.isConnectionPossible(mContext)) {
                    try {
                        final String url = update_engineer_dob;

                        ArrayList<NameValuePair> nameValuePairs = new ArrayList<>(2);
                        nameValuePairs.add(new BasicNameValuePair("eid", get_E_id(mContext)));
                        nameValuePairs.add(new BasicNameValuePair("user_type", get_user_type(mContext)));
                        nameValuePairs.add(new BasicNameValuePair("dob", dob));

                        print_Log_d("UPDATE_DOB_URL", url);
                        print_Log_d("UPDATE_DOB_PARAMS", nameValuePairs.toString());

                        POST_result = HTTPUtils.getDataByHTTP_POST(mContext, url, nameValuePairs);

                        print_Log_d("UPDATE_DOB_RESPONSE", POST_result);

                    } catch (Exception e) {
                        print_Log_d("UPDATE_DOB_ERROR", e.toString());
                        POST_result = "Network Failure";
                    }
                }
            } catch (Exception e) {
                e.printStackTrace();
            }
            return POST_result;
        }

        @Override
        public void onPostExecute(String result) {
            super.onPostExecute(result);
            dismissDialog();
            try {
                if (result != null && !result.isEmpty() && !result.equals("Network Failure")) {
                    JSONObject jo = new JSONObject(result);
                    Boolean status = jo.optBoolean("status");
                    String message = jo.optString("message");

                    if (status) {
                        Toast.makeText(mContext, message, Toast.LENGTH_SHORT).show();
                        isBirthdayDialogShown = false;
                        checkBirthdayStatus();
                    } else {
                        Toast.makeText(mContext, message, Toast.LENGTH_SHORT).show();
                        isBirthdayDialogShown = false;
                    }
                } else {
                    Toast.makeText(mContext, "Failed to update birthday", Toast.LENGTH_SHORT).show();
                    isBirthdayDialogShown = false;
                }
            } catch (Exception e) {
                e.printStackTrace();
                Toast.makeText(mContext, "Error updating birthday", Toast.LENGTH_SHORT).show();
                isBirthdayDialogShown = false;
            }
        }
    }
}

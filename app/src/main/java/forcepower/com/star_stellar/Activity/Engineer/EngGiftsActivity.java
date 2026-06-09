package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Patterns;
import android.view.Gravity;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.webkit.WebSettings;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.Button;
import android.widget.EditText;
import android.widget.FrameLayout;
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
import forcepower.com.star_stellar.Activity.Engineer.Adapter.GiftRvAdapter;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.Class.DividerItemDecoration;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.get_last_order_contact;
import static forcepower.com.star_stellar.Class.AllUrl.terms_api;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_show_my_gift;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_points_msg;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_points;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_points_msg;

public class EngGiftsActivity extends BaseActivity {
    Activity myActivity;
    private ArrayList<CommonHelper> pending_list = new ArrayList<>();

    TextView tv_points;
    boolean isLoading_p = false;
    GiftRvAdapter giftRvAdapter;
    RecyclerView rv_Pending;
    int page_no_P = 1, tot_count_P = 0, p_array_size = 0;

    EditText etEmail, etPhone;
    FrameLayout popupRoot;          // = popup_root
    LinearLayout llPopup;           // = llPopup (contact card)
    Button btnSave;
    String newEmail, newPhone, termsAndConditions, categoryId ;

    // T&C popup views
    private WebView webView;
    private LinearLayout reward_t_and_c_popup_layout;
    private TextView popup_check_box_text;
    private ImageView popup_check_box;
    private Button popup_t_c_submit_button;
    private int tc_popup_value = 0;

    // Intent to launch after T&C accepted
    private Intent pendingRedeemIntent;

    ProgressDialog progressDialogObj;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gifts);

        try {
            myActivity = EngGiftsActivity.this;

            // Header_View
            RelativeLayout rlHeaderView_Home = findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);

            RelativeLayout rlHeaderView = llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);

            TextView tvCaption = findViewById(R.id.tvCaption);
            tvCaption.setText("Gift Catalogue");
            ImageView ivBack = findViewById(R.id.ivBack);
            ivBack.setOnClickListener(v -> onBackPressed());

            // Contact popup + overlay
            popupRoot = findViewById(R.id.popup_root);  // FrameLayout
            llPopup = findViewById(R.id.llPopup);       // inner contact popup card
            etEmail = findViewById(R.id.etEmail);
            etPhone = findViewById(R.id.etPhone);
            btnSave = findViewById(R.id.btnSave);
            categoryId = getIntent().getStringExtra("category_id");

            // Header right points
            RelativeLayout rlForward = findViewById(R.id.rlForward);
            rlForward.setPadding(0, 0, 25, 0);
            tv_points = new TextView(myActivity);
            tv_points.setText(get_E_points_msg(myActivity));
            tv_points.setGravity(Gravity.RIGHT);
            tv_points.setTextColor(getResources().getColor(R.color.white));
            rlForward.addView(tv_points);
            rlForward.setVisibility(View.VISIBLE);

            // RecyclerView for gifts
            rv_Pending = findViewById(R.id.rv_Pending);
            GridLayoutManager mLayoutManager = new GridLayoutManager(this, 2);
            rv_Pending.setLayoutManager(mLayoutManager);
            rv_Pending.addItemDecoration(new DividerItemDecoration(getResources().getDrawable(R.drawable.divider)));
            giftRvAdapter = new GiftRvAdapter(pending_list, myActivity, newEmail, newPhone);

            mLayoutManager.setSpanSizeLookup(new GridLayoutManager.SpanSizeLookup() {
                @Override
                public int getSpanSize(int position) {
                    int viewType = giftRvAdapter.getItemViewType(position);
                    if (viewType == GiftRvAdapter.VIEW_TYPE_LOADING) {
                        return mLayoutManager.getSpanCount();
                    } else {
                        return 1;
                    }
                }
            });

            rv_Pending.setAdapter(giftRvAdapter);
            initScroll_p();

            if (isInternetConnected(myActivity)) {
                get_last_order_contact();      // pre-fill email/phone
                get_gift_details("fresh_g");   // load gifts
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }

            // Contact Submit Button
            btnSave.setOnClickListener(v -> {
                String email = etEmail.getText().toString().trim();
                String phone = etPhone.getText().toString().trim();

                if (!Patterns.EMAIL_ADDRESS.matcher(email).matches() || email.isEmpty()) {
                    Toast.makeText(myActivity, "Please enter a valid email", Toast.LENGTH_SHORT).show();
                } else if (phone.isEmpty() || phone.length() != 10) {
                    Toast.makeText(myActivity, "Please enter a valid phone number", Toast.LENGTH_SHORT).show();
                } else {
                    newEmail = email;
                    newPhone = phone;

                    if (giftRvAdapter != null) {
                        giftRvAdapter.updateContactInfo(newEmail, newPhone);
                    }

                    hideKeyboard(v);

                    // Hide whole overlay (contact popup + dim)
                    if (popupRoot != null) {
                        popupRoot.setVisibility(View.GONE);
                    }
                }
            });

            // --- T&C popup wiring ---
            reward_t_and_c_popup_layout = findViewById(R.id.reward_t_and_c_popup_layout);
            webView = findViewById(R.id.reward_t_and_c_popup_webview);
            popup_check_box_text = findViewById(R.id.popup_check_box_text);
            popup_check_box = findViewById(R.id.popup_check_box);
            popup_t_c_submit_button = findViewById(R.id.popup_t_c_submit_button);

            if (reward_t_and_c_popup_layout != null) {
                reward_t_and_c_popup_layout.setVisibility(View.GONE);
            }

            // WebView settings for T&C
            WebSettings webSettings = webView.getSettings();
            webSettings.setJavaScriptEnabled(true);
            webSettings.setLoadWithOverviewMode(true);
            webSettings.setUseWideViewPort(true);

            // Load T&C HTML
            fetchTermsAndConditions();

            // Tap on dim background of T&C popup: close popup + overlay
            reward_t_and_c_popup_layout.setOnClickListener(v -> {
                reward_t_and_c_popup_layout.setVisibility(View.GONE);
                if (popupRoot != null) popupRoot.setVisibility(View.GONE);
                pendingRedeemIntent = null;
            });

            popup_check_box_text.setOnClickListener(v -> toggleCheckbox());
            popup_check_box.setOnClickListener(v -> toggleCheckbox());

            popup_t_c_submit_button.setOnClickListener(v -> {
                if (tc_popup_value == 1) {
                    reward_t_and_c_popup_layout.setVisibility(View.GONE);
                    if (popupRoot != null) popupRoot.setVisibility(View.GONE);

                    if (pendingRedeemIntent != null) {
                        startActivity(pendingRedeemIntent);
                        pendingRedeemIntent = null;
                    } else {
                        Toast.makeText(this, "Something went wrong. Please try again.", Toast.LENGTH_LONG).show();
                    }
                } else {
                    Toast.makeText(this, "Accept terms & condition first", Toast.LENGTH_LONG).show();
                }
            });

        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        tv_points.setText(get_E_points_msg(myActivity));
    }

    private void hideKeyboard(View view) {
        InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
        if (imm != null) {
            imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }
    }

    private void toggleCheckbox() {
        if (tc_popup_value == 0) {
            tc_popup_value = 1;
            popup_check_box.setImageResource(R.drawable.baseline_check_box_24);
        } else {
            tc_popup_value = 0;
            popup_check_box.setImageResource(R.drawable.baseline_check_box_outline_blank_24);
        }
    }

    // 👉 Called from adapter when Redeem is clicked
    public void showTermsPopup(Intent redeemIntent) {
        this.pendingRedeemIntent = redeemIntent;

        // Reset checkbox & visibility
        tc_popup_value = 0;
        if (popup_check_box != null) {
            popup_check_box.setImageResource(R.drawable.baseline_check_box_outline_blank_24);
        }

        // Hide contact popup card if it's visible
        if (llPopup != null) {
            llPopup.setVisibility(View.GONE);
        }

        // Show overlay + T&C popup
        if (popupRoot != null) {
            popupRoot.setVisibility(View.VISIBLE);
        }
        if (reward_t_and_c_popup_layout != null) {
            reward_t_and_c_popup_layout.setVisibility(View.VISIBLE);
        }

        print_Log_d("TC_POPUP", "showTermsPopup called");
    }

    public void fetchTermsAndConditions() {
        try {
            String url = terms_api;

            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);

            print_Log_d("terms_U ", url);

            client.get(url, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    final String str = new String(responseBody);
                    try {
                        print_Log_d("terms_R ", str + "");

                        final JSONObject reader = new JSONObject(str);

                        termsAndConditions = reader.optString("content");
                        print_Log_d("terms_RR ", termsAndConditions);

                        String htmlContent = termsAndConditions != null ? termsAndConditions : "";

                        String styledHtml = "<html>" +
                                "<head>" +
                                "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">" +
                                "<style>" +
                                "body { font-size: 14px; color: #333333; margin: 0; padding: 0; font-family: sans-serif; }" +
                                "* { max-width: 100%; }" +
                                "</style>" +
                                "</head>" +
                                "<body>" +
                                htmlContent +
                                "</body>" +
                                "</html>";

                        webView.loadDataWithBaseURL(null, styledHtml, "text/html", "UTF-8", null);

                        // Adjust WebView height based on content
                        webView.setWebViewClient(new WebViewClient() {
                            @Override
                            public void onPageFinished(WebView view, String url) {
                                super.onPageFinished(view, url);

                                view.evaluateJavascript(
                                        "(function() { return document.body.scrollHeight; })();",
                                        height -> {
                                            try {
                                                if (height != null && !height.equals("null")) {
                                                    String cleanHeight = height.replace("\"", "");
                                                    int contentHeight = Integer.parseInt(cleanHeight);
                                                    float density = getResources().getDisplayMetrics().density;
                                                    int minHeight = (int) (50 * density);
                                                    int maxHeight = (int) (300 * density);

                                                    ViewGroup.LayoutParams layoutParams = webView.getLayoutParams();
                                                    if (contentHeight < minHeight) {
                                                        layoutParams.height = minHeight;
                                                    } else if (contentHeight > maxHeight) {
                                                        layoutParams.height = maxHeight;
                                                    } else {
                                                        layoutParams.height = contentHeight;
                                                    }
                                                    webView.setLayoutParams(layoutParams);
                                                } else {
                                                    ViewGroup.LayoutParams layoutParams = webView.getLayoutParams();
                                                    layoutParams.height = (int) (150 * getResources().getDisplayMetrics().density);
                                                    webView.setLayoutParams(layoutParams);
                                                }
                                            } catch (NumberFormatException e) {
                                                e.printStackTrace();
                                                ViewGroup.LayoutParams layoutParams = webView.getLayoutParams();
                                                layoutParams.height = (int) (150 * getResources().getDisplayMetrics().density);
                                                webView.setLayoutParams(layoutParams);
                                            }
                                        }
                                );
                            }
                        });

                    } catch (final Exception e) {
                        e.printStackTrace();
                    }
                }

                @Override
                public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                    print_Log_d("terms_Err ", error != null ? error.toString() : "Unknown error");
                }
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void get_gift_details(final String type) {
        if (!type.matches("add_p"))
            loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("page_no", page_no_P + "");
        params.put("category_id", categoryId + "");

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_show_my_gift, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("gift_ ", reader + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (page_no_P == 1) {
                            tot_count_P = reader.optInt("e_points");
                        }
                        set_E_points(myActivity, reader.optString("e_points"));
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
                                cdh.setItem6(e.getString("button_status")); //button_status
                                cdh.setItem7(reader.optString("e_points")); //e_points
                                cdh.setItem8(e.getString("total_point")); //e_points
                                cdh.setboolValue(e.getBoolean("is_email_required")); //is_email_required

                                pending_list.add(cdh);
                            }
                            if (type.matches("add_p")) {
                                print_Log_d("productt_", pending_list.toString());
                                giftRvAdapter.notifyDataSetChanged();
                            } else {
                                print_Log_d("product_", pending_list.toString());
                                giftRvAdapter = new GiftRvAdapter(pending_list, myActivity, newEmail, newPhone);
                                rv_Pending.setAdapter(giftRvAdapter);
                            }

                            p_array_size = giftRvAdapter.getItemCount_();
                            set_E_points(myActivity, reader.optString("e_points"));
                            set_E_points_msg(myActivity, reader.optString("e_points_msg"));
                            tv_points.setText(reader.optString("e_points_msg"));
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

    public void get_last_order_contact() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity)); // your engineer ID

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(get_last_order_contact, params, new AsyncHttpResponseHandler() {

            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                dismissDialog(); // hide loader
                try {
                    final String str = new String(responseBody);
                    print_Log_d("get_last_order_contact", str);
                    print_Log_d("get_last_order_contact", get_last_order_contact);

                    JSONObject reader = new JSONObject(str);
                    if (reader.optString("status").equalsIgnoreCase("YES")) {

                        etEmail.setText(reader.optString("user_email"));
                        etPhone.setText(reader.optString("phone"));

                    } else {
                        Toast.makeText(myActivity, "Something Went Wrong", Toast.LENGTH_SHORT).show();
                    }

                } catch (Exception e) {
                    e.printStackTrace();
                    Toast.makeText(myActivity, "Something Went Wrong", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                print_Log_d("get_last_order_contact_error", error.toString());
                Toast.makeText(myActivity, "Something Went Wrong", Toast.LENGTH_SHORT).show();
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
        handler.postDelayed(() -> {
            if (isInternetConnected(myActivity)) {
                if (page_no_P > 0) {
                    get_gift_details("add_p");
                } else {
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
        }, 3000);
    }

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

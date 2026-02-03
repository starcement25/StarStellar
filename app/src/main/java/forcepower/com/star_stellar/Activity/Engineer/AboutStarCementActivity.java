package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.Color;
import android.net.MailTo;
import android.os.Bundle;
import android.view.View;
import android.webkit.WebResourceError;
import android.webkit.WebResourceRequest;
import android.webkit.WebView;
import android.webkit.WebViewClient;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.about_weblink;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class AboutStarCementActivity extends BaseActivity {
    Activity myActivity;
    WebView wv_termsNcondi;
    boolean loadingFinished = true;
    boolean redirect = false;
    TextView tv_empty_View;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_about_star);

        try {
            myActivity = AboutStarCementActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("About Star Cement");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            tv_empty_View = (TextView) findViewById(R.id.tv_empty_View);
            wv_termsNcondi = (WebView) findViewById(R.id.wv_termsNcondi);
            wv_termsNcondi.setWebViewClient(new ShowMyBrowser());
            wv_termsNcondi.getSettings().setJavaScriptEnabled(true);
            wv_termsNcondi.getSettings().setGeolocationEnabled(true);
            wv_termsNcondi.getSettings().setLoadsImagesAutomatically(true);
            wv_termsNcondi.setScrollBarStyle(View.SCROLLBARS_INSIDE_OVERLAY);
            if (isInternetConnected(myActivity)) {
                wv_termsNcondi.setVisibility(View.VISIBLE);
                tv_empty_View.setVisibility(View.GONE);
            } else {
                wv_termsNcondi.setVisibility(View.GONE);
                tv_empty_View.setVisibility(View.VISIBLE);
            }
            if (!about_weblink.matches("")) {
                //SHOW LOADING
                loadDialog();
                wv_termsNcondi.loadUrl(about_weblink);
            } else {
                tv_empty_View.setVisibility(View.VISIBLE);
            }
            wv_termsNcondi.setWebViewClient(new WebViewClient() {

                @Override
                public boolean shouldOverrideUrlLoading(WebView view, String urlNewString) {
                    if (!loadingFinished) {
                        redirect = true;
                    }

                    loadingFinished = false;

                    if (urlNewString.startsWith("mailto:")) {
                        MailTo mt = MailTo.parse(urlNewString);
                        Intent i = new Intent(Intent.ACTION_SEND);
                        i.setType("text/plain");
                        i.putExtra(Intent.EXTRA_EMAIL, new String[]{mt.getTo()});
                        i.putExtra(Intent.EXTRA_SUBJECT, mt.getSubject());
                        i.putExtra(Intent.EXTRA_CC, mt.getCc());
                        i.putExtra(Intent.EXTRA_TEXT, mt.getBody());
                        startActivity(i);
                        view.reload();
                        return true;
                    }
                    //SHOW LOADING
                    loadDialog();
                    view.loadUrl(urlNewString);
                    return true;
                }

                @Override
                public void onReceivedError(WebView view, WebResourceRequest request, WebResourceError error) {
                    //Your code to do
                    view.setVisibility(View.INVISIBLE);
                    if (progressDialogObj != null && progressDialogObj.isShowing())
                        progressDialogObj.dismiss();

//                    tv_empty_View.setVisibility(View.VISIBLE);
                }

                @Override
                public void onPageStarted(WebView view, String url, Bitmap facIcon) {
                    loadingFinished = false;

                }

                @Override
                public void onPageFinished(WebView view, String url) {
//                    if (!redirect) {
//                        loadingFinished = true;
//                    }
//
//                    if (loadingFinished && !redirect) {
//                        //HIDE LOADING IT HAS FINISHED

                    if (progressDialogObj != null && progressDialogObj.isShowing())
                        progressDialogObj.dismiss();
//                    } else {
//                        redirect = false;
//                    }

                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private class ShowMyBrowser extends WebViewClient {
        @Override
        public boolean shouldOverrideUrlLoading(WebView view, String url) {
            //SHOW LOADING
            loadDialog();
            view.loadUrl(url);
            return true;
        }
    }

    ProgressDialog progressDialogObj;

    public void loadDialog() {
        if (progressDialogObj != null && progressDialogObj.isShowing())
            progressDialogObj.dismiss();
        progressDialogObj = new ProgressDialog(myActivity);
        progressDialogObj.setIndeterminate(true);
        progressDialogObj.setProgressStyle(android.R.style.Widget_ProgressBar_Small);
//        progressDialogObj.setIndeterminateDrawable(ContextCompat.getDrawable(mContext,R.drawable.log));
        progressDialogObj.setCancelable(false);
        progressDialogObj.setCanceledOnTouchOutside(false);
        progressDialogObj.show();
    }

    @Override
    public void onBackPressed() {
        finish();
    }
}

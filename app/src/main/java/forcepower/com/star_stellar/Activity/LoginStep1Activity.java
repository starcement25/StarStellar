package forcepower.com.star_stellar.Activity;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Paint;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.ws_generate_otp_for_engineer_and_te_login;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.isValidMobile;
import static forcepower.com.star_stellar.Class.CommonClass.mobile;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.user_type;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class LoginStep1Activity extends BaseActivity {
    EditText et_L_mobile;
    Activity myActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_1);
        try {
            myActivity = LoginStep1Activity.this;
            et_L_mobile = (EditText) findViewById(R.id.et_L_mobile);

            RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
            rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));

            TextView tv_TC = (TextView) findViewById(R.id.tv_TC);
            TextView tv_login_as = (TextView) findViewById(R.id.tv_login_as);
            tv_TC.setPaintFlags(tv_TC.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            tv_login_as.setPaintFlags(tv_login_as.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            if (BuildConfig.DEBUG) {
                myIssues(null);
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void myIssues(View view) {
        try {
            AlertDialog.Builder AlertDG = new AlertDialog.Builder(myActivity, R.style.MyDialog);
            final CharSequence[] items = {"TE", "Engg"};

            AlertDG.setItems(items, new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int item) {
                    if (items[item].toString().equalsIgnoreCase("TE")) {
                        et_L_mobile.setText("9874450813");
                    } else if (items[item].toString().equalsIgnoreCase("Engg")) {
                        et_L_mobile.setText("9233974090");
                    }
                }
            });
            AlertDG.setNegativeButton("Cancel", new DialogInterface.OnClickListener() {

                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            });
            AlertDG.setCancelable(true);
            AlertDG.create().show();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public void login_1(View view) {
        String val = et_L_mobile.getText().toString().trim() + "";
        if (!isValidMobile(val)) {
            Toast.makeText(myActivity, "Please enter valid mobile number", Toast.LENGTH_SHORT).show();
            et_L_mobile.requestFocus();
        } else if (val.length() != 10) {
            Toast.makeText(myActivity, "Please enter 10 digit mobile number", Toast.LENGTH_SHORT).show();
            et_L_mobile.requestFocus();
        } else if (!isInternetConnected(myActivity)) {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        } else {
            //
            login();
        }
    }

    public void login() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("mobile", et_L_mobile.getText().toString());

        //mobile,user_type
         print_Log_d("_DOWNLOAD_U login", ws_generate_otp_for_engineer_and_te_login + "");
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_generate_otp_for_engineer_and_te_login, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("_DOWNLOAD_R login", str + "");
                    print_Log_d("_DOWNLOAD_P login", params + "");
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        user_type = reader.getString("user_type");
                        mobile = et_L_mobile.getText().toString();
                        Intent intent = new Intent(myActivity, OtpActivity.class);
                        intent.putExtra("user_type", user_type);
                        intent.putExtra("mobile", mobile);
                        startActivity(intent);
                    } else {
                        Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
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
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
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

    public void continueTC(View view) {
        Intent intent = new Intent(myActivity, TermsConditionActivity.class);
        startActivity(intent);
    }

    public void signUp(View view) {
        user_type = "ENGINEER";
        Intent intent = new Intent(myActivity, LoginTEcodeActivity.class);
        intent.putExtra("user_type", user_type);
        startActivity(intent);
    }

    boolean doubleBackToExitPressedOnce = false;

    @Override
    public void onBackPressed() {
        if (doubleBackToExitPressedOnce) {
            finishAffinity();
            return;
        }

        this.doubleBackToExitPressedOnce = true;
        Toast.makeText(this, "Please press BACK again to exit", Toast.LENGTH_SHORT).show();

        new Handler().postDelayed(new Runnable() {

            @Override
            public void run() {
                doubleBackToExitPressedOnce = false;
            }
        }, 2000);
    }
}

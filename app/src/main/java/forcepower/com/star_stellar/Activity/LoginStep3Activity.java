package forcepower.com.star_stellar.Activity;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.OtpActivity_Signup;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_generate_otp_for_engineer_login;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.isValidMobile;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class LoginStep3Activity extends BaseActivity {
    EditText et_te_phone;
    Activity myActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_3);
        try {
            myActivity = LoginStep3Activity.this;
            et_te_phone = (EditText) findViewById(R.id.et_te_phone);

            RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
            rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));

            if (BuildConfig.DEBUG) {
                et_te_phone.setText("7278212381");
                et_te_phone.setText("9831722939");
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void login_3(View view) {
        String val = et_te_phone.getText().toString().trim() + "";
        if (!isValidMobile(val)) {
            Toast.makeText(myActivity, "Please enter valid mobile number", Toast.LENGTH_SHORT).show();
            et_te_phone.requestFocus();
        } else if (val.length() != 10) {
            Toast.makeText(myActivity, "Please enter 10 digit mobile number", Toast.LENGTH_SHORT).show();
            et_te_phone.requestFocus();
        } else if (!isInternetConnected(myActivity)) {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        } else {
            continueLogin(et_te_phone.getText().toString());
        }
    }

    public void loginAsTE(View view) {
        Toast.makeText(myActivity, "Coming soon...", Toast.LENGTH_SHORT).show();
    }

    public void continueLogin(final String mobile) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", getIntent().getStringExtra("te_code"));
        params.put("e_name", getIntent().getStringExtra("te_name"));
        params.put("e_email", getIntent().getStringExtra("te_email"));
        params.put("mobile", mobile);

                    print_Log_d("_DOWNLOAD_U continueLogin", ws_generate_otp_for_engineer_login + "");
                    print_Log_d("_DOWNLOAD_P continueLogin", params + "");
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_generate_otp_for_engineer_login, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("_DOWNLOAD_R continueLogin", str + "");
//                    {"process_status":"YES","process_message":"OTP has been sent to your mobile number."}

                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        Intent intent = new Intent(myActivity, OtpActivity_Signup.class);
                        intent.putExtra("te_code", getIntent().getStringExtra("te_code"));
                        intent.putExtra("te_name", getIntent().getStringExtra("te_name"));
                        intent.putExtra("te_email", getIntent().getStringExtra("te_email"));
                        intent.putExtra("mobile", mobile);
                        startActivity(intent);
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
}

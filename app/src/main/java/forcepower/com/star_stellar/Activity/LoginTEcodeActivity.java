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
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_check_te_code_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class LoginTEcodeActivity extends BaseActivity {
    EditText et_te_code;
    Activity myActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_te_code);
        try {
            myActivity = LoginTEcodeActivity.this;
            et_te_code = (EditText) findViewById(R.id.et_te_code);


            RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
            rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));

            if (BuildConfig.DEBUG) {
                et_te_code.setText("TE001");
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void login_step_2(View view) {
        if (et_te_code.getText().toString().trim().matches("")) {
            Toast.makeText(myActivity, "Please enter TE code", Toast.LENGTH_SHORT).show();
        } else if (!isInternetConnected(myActivity)) {
            Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
        } else {
            continueLogin();
        }
    }

    public void continueLogin() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("te_code", et_te_code.getText().toString());
 print_Log_d("_DOWNLOAD_U continueLogin", ws_check_te_code_for_engineer + "");
 print_Log_d("_DOWNLOAD_P continueLogin", params + "");
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_check_te_code_for_engineer, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("_DOWNLOAD_R continueLogin", str + "");
//                  {"process_status":"YES","process_message":"The TE CODE exists."}

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        Intent intent = new Intent(myActivity, LoginSocialActivity.class);
                        intent.putExtra("te_code", et_te_code.getText().toString());
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
}

package forcepower.com.star_stellar.Activity;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.inputmethod.EditorInfo;
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
import forcepower.com.star_stellar.Activity.Engineer.EngineerHomeActivity;
import forcepower.com.star_stellar.Activity.TE.TeHomeActivity;
import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.SharedPrefData;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_generate_otp_for_engineer_and_te_login;
import static forcepower.com.star_stellar.Class.AllUrl.ws_login_with_otp_for_engineer_and_te;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.msg_Dialog;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.user_type;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceId;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_firebase_token;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_ALL;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_email;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_loginJsonData;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_mobile;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_loginJsonData;
import static forcepower.com.star_stellar.Class.SharedPrefData.setLoginStatus;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_email;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_mobile;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_name;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_code;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_TE_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_user_type;

public class OtpActivity extends BaseActivity implements TextWatcher, View.OnKeyListener, View.OnFocusChangeListener {
    private EditText et_digit1, et_digit2, et_digit3, et_digit4;//In this et_digit1 is Most significant digit and et_digit4 is least significant digit
    private int whoHasFocus, resendotp_count = 1;
    char[] code = new char[4];//Store the digits in charArray.
    Activity myActivity;
    private TextView tv_resend_OTP;
    private String mobile = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_otp);

        myActivity = this;
        mobile = getIntent().getStringExtra("mobile");
        RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
        rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));

        et_digit1 = (EditText) findViewById(R.id.et_otp_1);
        et_digit2 = (EditText) findViewById(R.id.et_otp_2);
        et_digit3 = (EditText) findViewById(R.id.et_otp_3);
        et_digit4 = (EditText) findViewById(R.id.et_otp_4);
        setListners();

        tv_resend_OTP = (TextView) findViewById(R.id.tv_resend_OTP);
        tv_resend_OTP.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (!isInternetConnected(myActivity)) {
                    Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                } else {
                    tv_resend_OTP.setEnabled(false);
                    tv_resend_OTP.setTextColor(Color.parseColor("#d3d3d3"));
                    reSend_Otp();
                }
            }
        });
    }

    private void setListners() {
        et_digit1.addTextChangedListener(this);
        et_digit2.addTextChangedListener(this);
        et_digit3.addTextChangedListener(this);
        et_digit4.addTextChangedListener(this);

        et_digit1.setOnKeyListener(this);
        et_digit2.setOnKeyListener(this);
        et_digit3.setOnKeyListener(this);
        et_digit4.setOnKeyListener(this);

        et_digit1.setOnFocusChangeListener(this);
        et_digit2.setOnFocusChangeListener(this);
        et_digit3.setOnFocusChangeListener(this);
        et_digit4.setOnFocusChangeListener(this);
        et_digit4.setOnEditorActionListener(new TextView.OnEditorActionListener() {
            @Override
            public boolean onEditorAction(TextView v, int actionId, KeyEvent event) {
                if ((event != null && (event.getKeyCode() == KeyEvent.KEYCODE_ENTER)) || (actionId == EditorInfo.IME_ACTION_DONE)) {
                    confirm_otp(null);
                }
                return false;
            }
        });
    }

    @Override
    public void onFocusChange(View v, boolean hasFocus) {
        switch (v.getId()) {
            case R.id.et_otp_1:
                whoHasFocus = 1;
                break;

            case R.id.et_otp_2:
                whoHasFocus = 2;
                break;

            case R.id.et_otp_3:
                whoHasFocus = 3;
                break;

            case R.id.et_otp_4:
                whoHasFocus = 4;
                break;

            default:
                break;
        }
    }

    @Override
    public void beforeTextChanged(CharSequence s, int start, int count, int after) {
    }

    @Override
    public void onTextChanged(CharSequence s, int start, int before, int count) {
    }

    @Override
    public void afterTextChanged(Editable s) {
        switch (whoHasFocus) {
            case 1:
                if (!et_digit1.getText().toString().isEmpty()) {
                    code[0] = et_digit1.getText().toString().charAt(0);
                    et_digit2.requestFocus();
                }
                break;

            case 2:
                if (!et_digit2.getText().toString().isEmpty()) {
                    code[1] = et_digit2.getText().toString().charAt(0);
                    et_digit3.requestFocus();
                }
                break;

            case 3:
                if (!et_digit3.getText().toString().isEmpty()) {
                    code[2] = et_digit3.getText().toString().charAt(0);
                    et_digit4.requestFocus();
                }
                break;

            case 4:
                if (!et_digit4.getText().toString().isEmpty()) {
                    code[3] = et_digit4.getText().toString().charAt(0);
                    confirm_otp(null);
                }
                break;

            default:
                break;
        }
    }

    @Override
    public boolean onKey(View v, int keyCode, KeyEvent event) {
        if (event.getAction() == KeyEvent.ACTION_DOWN) {
            if (keyCode == KeyEvent.KEYCODE_DEL) {
                switch (v.getId()) {
                    case R.id.et_otp_2:
                        if (et_digit2.getText().toString().isEmpty())
                            et_digit1.requestFocus();
                        break;

                    case R.id.et_otp_3:
                        if (et_digit3.getText().toString().isEmpty())
                            et_digit2.requestFocus();
                        break;

                    case R.id.et_otp_4:
                        if (et_digit4.getText().toString().isEmpty())
                            et_digit3.requestFocus();
                        break;

                    default:
                        break;
                }
            } else {
                switch (v.getId()) {
                    case R.id.et_otp_2:
                        if (!et_digit2.getText().toString().isEmpty())
                            et_digit3.requestFocus();
                        break;

                    case R.id.et_otp_3:
                        if (!et_digit3.getText().toString().isEmpty())
                            et_digit4.requestFocus();
                        break;

                    case R.id.et_otp_1:
                        if (!et_digit1.getText().toString().isEmpty())
                            et_digit2.requestFocus();
                        break;

                    default:
                        break;
                }
            }
        }
        return false;
    }

    public void confirm_otp(View view) {
        if (!et_digit1.getText().toString().matches("") &&
                !et_digit2.getText().toString().matches("") &&
                !et_digit3.getText().toString().matches("") &&
                !et_digit4.getText().toString().matches("")) {
            if (!isInternetConnected(myActivity)) {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            } else {
                continueLoginOTP(et_digit1.getText().toString() +
                        et_digit2.getText().toString() +
                        et_digit3.getText().toString() +
                        et_digit4.getText().toString());
            }
        } else {
            Toast.makeText(myActivity, "Please enter 4 digit OTP", Toast.LENGTH_SHORT).show();
        }
    }

    public void continueLoginOTP(String otp) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("user_type", user_type);
        params.put("mobile", mobile);
        params.put("otp", otp);
        params.put("device_id", SharedPrefData.getDeviceId(myActivity));
        params.put("registration_id", get_firebase_token(myActivity));
        params.put("device_type", "ANDROID");
        params.put("app_version", BuildConfig.VERSION_NAME);

//mobile,user_type,otp,device_id,registration_id,device_type
//device_type = ANDROID/IOS
//Default OTP = 1010
//user_type = TE or ENGINEER

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_login_with_otp_for_engineer_and_te, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("_DOWNLOAD_U continueLoginOTP ", ws_login_with_otp_for_engineer_and_te + "");
                    print_Log_d("_DOWNLOAD_P continueLoginOTP ", params + "");
                    print_Log_d("_DOWNLOAD_R continueLoginOTP ", str + "");

                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        setLoginStatus(myActivity, true);
                        set_user_type(myActivity, user_type);
                        set_TE_code(myActivity, reader.optString("the_te_code"));

                        if (reader.getString("user_type").matches("TE")) {
                            set_TE_loginJsonData(myActivity, str);
                            set_TE_id(myActivity, reader.optString("the_te_id"));
                            set_TE_name(myActivity, reader.optString("the_te_name"));
                            set_TE_mobile(myActivity, reader.optString("the_te_mobile_no"));
                            set_TE_email(myActivity, reader.optString("the_te_email"));
//                            set_e_profile_image(myActivity, reader.optString("te_profile_image"));

                            //
                            startActivity(new Intent(myActivity, TeHomeActivity.class));
                        } else {
                            set_E_loginJsonData(myActivity, str);
                            set_E_id(myActivity, reader.optString("the_engineer_id"));
                            set_E_name(myActivity, reader.optString("e_name"));
                            set_E_mobile(myActivity, reader.optString("e_mobile"));
                            set_E_code(myActivity, reader.optString("te_code"));
                            set_E_email(myActivity, reader.optString("e_email"));
                            set_ALL(myActivity,
                                    reader.optString("e_dob"),
                                    reader.optString("e_dom"),
                                    reader.optString("e_address"),
                                    reader.optString("e_pin"),
                                    reader.optString("e_state"),
                                    reader.optString("e_city_town")
                            );
                            //
                            startActivity(new Intent(myActivity, EngineerHomeActivity.class));
//                            set_e_profile_image(myActivity, reader.optString("te_profile_image"));
                        }
                        finishAffinity();
                    } else {
                        msg_Dialog(myActivity, reader.optString("process_message"), false);

                        et_digit4.dispatchKeyEvent(new KeyEvent(KeyEvent.ACTION_DOWN, KeyEvent.KEYCODE_DEL));
                        et_digit3.dispatchKeyEvent(new KeyEvent(KeyEvent.ACTION_DOWN, KeyEvent.KEYCODE_DEL));
                        et_digit2.dispatchKeyEvent(new KeyEvent(KeyEvent.ACTION_DOWN, KeyEvent.KEYCODE_DEL));
                        et_digit1.dispatchKeyEvent(new KeyEvent(KeyEvent.ACTION_DOWN, KeyEvent.KEYCODE_DEL));
                        et_digit1.requestFocus();
//                        Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
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

    @Override
    public void onBackPressed() {
        finish();
    }

    public void reSend_Otp() {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("user_type", user_type);
        params.put("mobile", mobile);

        //mobile,user_type
 print_Log_d("_DOWNLOAD_U reSend_Otp", ws_generate_otp_for_engineer_and_te_login + "");
  print_Log_d("_DOWNLOAD_P reSend_Otp", params + "");

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_generate_otp_for_engineer_and_te_login, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("_DOWNLOAD_R reSend_Otp", str + "");
                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                } catch (final Exception e) {
                    e.printStackTrace();
                } finally {
                    dismissDialog();
                    tv_resend_OTP.postDelayed(new Runnable() {
                        @Override
                        public void run() {
                            tv_resend_OTP.setEnabled(true);
                            tv_resend_OTP.setTextColor(Color.BLACK);
                        }
                    }, 30000);
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
            }


        });
    }

    public void resendOTP(View view) {
        if (resendotp_count == 1 || resendotp_count == 2) {
            if (isInternetConnected(myActivity)) {
                reSend_Otp();
                resendotp_count++;
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        } else {
            Toast.makeText(myActivity, "You have exceeded the maximum times", Toast.LENGTH_SHORT).show();
        }
    }
}

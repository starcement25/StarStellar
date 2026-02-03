package forcepower.com.star_stellar.Activity;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.os.StrictMode;
import android.provider.Settings;
import android.util.Log;
import android.view.View;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import androidx.annotation.NonNull;

//import com.facebook.AccessToken;
//import com.facebook.CallbackManager;
//import com.facebook.FacebookCallback;
//import com.facebook.FacebookException;
//import com.facebook.FacebookSdk;
//import com.facebook.GraphRequest;
//import com.facebook.GraphResponse;
//import com.facebook.appevents.AppEventsLogger;
//import com.facebook.login.LoginManager;
//import com.facebook.login.LoginResult;
//import com.facebook.login.widget.LoginButton;
import com.google.android.gms.auth.api.Auth;
import com.google.android.gms.auth.api.signin.GoogleSignIn;
import com.google.android.gms.auth.api.signin.GoogleSignInAccount;
import com.google.android.gms.auth.api.signin.GoogleSignInClient;
import com.google.android.gms.auth.api.signin.GoogleSignInOptions;
import com.google.android.gms.auth.api.signin.GoogleSignInResult;
import com.google.android.gms.common.ConnectionResult;
import com.google.android.gms.common.api.ApiException;
import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;

import org.json.JSONException;
import org.json.JSONObject;

import java.net.URL;
import java.util.Arrays;
import java.util.HashMap;
import java.util.Map;

import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.RootApplication;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.validEmailFormat;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class LoginSocialActivity extends BaseActivity implements GoogleApiClient.OnConnectionFailedListener {
    EditText et_te_name, et_te_email;
    Activity myActivity;
    public static final int RC_SIGN_IN = 9001;
    private GoogleSignInClient googleSignInClient;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login_social);
        try {
            myActivity = LoginSocialActivity.this;
            et_te_name = (EditText) findViewById(R.id.et_te_name);
            et_te_email = (EditText) findViewById(R.id.et_te_email);

            RelativeLayout rl_Star_Logo = (RelativeLayout) findViewById(R.id.rl_Star_Logo);
            rl_Star_Logo.getLayoutParams().height = (get_Banner_Height(myActivity) + get_Header_Height(myActivity));

            if (BuildConfig.DEBUG) {
                et_te_name.setText("Suranjit Das");
                et_te_email.setText("suranjitd@coral.in");
            }

            GoogleSignInOptions gso = new GoogleSignInOptions.Builder(GoogleSignInOptions.DEFAULT_SIGN_IN)
//                .requestIdToken("AIzaSyBex8TnVcYiRpjzoOghe8yF9W2uLEEYgRk")
                    .requestEmail()
                    .build();
            googleSignInClient = GoogleSignIn.getClient(this, gso);
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void social_login(View view) {
        if (et_te_name.getText().toString().trim().matches("")) {
            Toast.makeText(myActivity, "Please enter TE name", Toast.LENGTH_SHORT).show();
        } else if (!validEmailFormat(et_te_email.getText().toString())) {
            Toast.makeText(myActivity, "Please enter proper email", Toast.LENGTH_SHORT).show();
        } else {
            Intent intent = new Intent(myActivity, LoginStep3Activity.class);
            intent.putExtra("te_code", getIntent().getStringExtra("te_code"));
            intent.putExtra("te_name", et_te_name.getText().toString());
            intent.putExtra("te_email", et_te_email.getText().toString());
            startActivity(intent);
        }
    }

    public void google_login(View view) {
        if (isInternetConnected(myActivity)) {
            Intent signInIntent = googleSignInClient.getSignInIntent();
            myActivity.startActivityForResult(signInIntent, RC_SIGN_IN);
        } else {
            Toast.makeText(getApplicationContext(), "No Network", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onActivityResult(int requestCode, int resultCode, Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        print_Log_d("_DOWNLOAD_R VOLLEY_RESULT_GOOGLE_603", resultCode + "");
        if (resultCode == Activity.RESULT_OK) {
            if (requestCode == RC_SIGN_IN) {
                try {
                    // The Task returned from this call is always completed, no need to attach
                    // a listener.
                    Task<GoogleSignInAccount> task = GoogleSignIn.getSignedInAccountFromIntent(data);
                    GoogleSignInAccount account = task.getResult(ApiException.class);
                    googleLogin(account.getDisplayName() + "", account.getEmail() + "");
                    print_Log_d("_DOWNLOAD_R VOLLEY_RESULT_GOOGLE_608", account.getDisplayName() + "");
                    print_Log_d("_DOWNLOAD_R VOLLEY_RESULT_GOOGLE_608", account.getEmail() + "");
                } catch (ApiException e) {
                    Log.d("", "signInResult:failed code=" + e.getStatusCode());
                }

            }
        }
    }

    public void googleLogin(final String get_UserName, final String get_UserEmail) {
        print_Log_d("_DOWNLOAD_R VOLLEY_RESULT_GOOGLE_", get_UserName + " " + get_UserEmail);
        socialLogin(get_UserName + "", get_UserEmail + "", "gmail");
    }

    public void socialLogin(String cust_name, String cust_email, String login_type) {
        try {
            Map<String, String> jsonObject = new HashMap<>();
            jsonObject.put("cust_name", cust_name + "");
            jsonObject.put("cust_email", cust_email + "");
            jsonObject.put("login_type", login_type + "");
            print_Log_d("_DOWNLOAD_P socialLogin", jsonObject.toString() + "");

            if (!cust_name.matches("") && !cust_email.matches("") && validEmailFormat(cust_email)) {
                Intent intent = new Intent(myActivity, LoginStep3Activity.class);
                intent.putExtra("te_code", getIntent().getStringExtra("te_code"));
                intent.putExtra("te_name", cust_name);
                intent.putExtra("te_email", cust_email);
                startActivity(intent);
            } else {
                Toast.makeText(myActivity, "Try again", Toast.LENGTH_SHORT).show();
                recreate();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onStart() {
        super.onStart();
        GoogleSignInAccount alreadyloggedAccount = GoogleSignIn.getLastSignedInAccount(this);
        if (alreadyloggedAccount != null) {
            googleSignInClient.signOut().addOnCompleteListener(new OnCompleteListener<Void>() {
                @Override
                public void onComplete(@NonNull Task<Void> task) {
                }
            });
        } else {
            Log.d("", "Not logged in");
        }
    }

    @Override
    public void onConnectionFailed(@NonNull ConnectionResult connectionResult) {}
}

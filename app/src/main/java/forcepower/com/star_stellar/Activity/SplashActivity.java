package forcepower.com.star_stellar.Activity;

import android.Manifest;
import android.app.Activity;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.appcompat.app.AlertDialog;
import androidx.core.content.ContextCompat;

import android.telephony.TelephonyManager;
import android.util.DisplayMetrics;
import android.util.Log;
import android.view.Window;
import android.view.WindowManager;
import android.widget.ImageView;
import android.widget.RelativeLayout;
import android.widget.Toast;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.OnSuccessListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.iid.FirebaseInstanceId;
import com.google.firebase.iid.InstanceIdResult;
import com.google.firebase.messaging.FirebaseMessaging;

import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;

import forcepower.com.star_stellar.Activity.Engineer.EngineerHomeActivity;
import forcepower.com.star_stellar.Activity.TE.TeHomeActivity;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.MarshMallowPermission;
import forcepower.com.star_stellar.Class.SharedPrefData;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceHeight;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceId;
import static forcepower.com.star_stellar.Class.SharedPrefData.getLoginStatus;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_firebase_token;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_user_type;
import static forcepower.com.star_stellar.Class.SharedPrefData.setDeviceHeight;
import static forcepower.com.star_stellar.Class.SharedPrefData.setDeviceId;
import static forcepower.com.star_stellar.Class.SharedPrefData.setDeviceWidth;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_Banner_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_firebase_token;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_server_current_date;

public class SplashActivity extends BaseActivity {
    private Activity myActivity;
    private static final int REQUEST_NOTIFICATION_PERMISSION = 1001;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main_splash);
        requestNotificationPermissionIfNeeded();
    }

    @Override
    public void onRequestPermissionsResult(int requestCode,
                                           @NonNull String[] permissions,
                                           @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == REQUEST_NOTIFICATION_PERMISSION) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                // Permission granted
                callNextFunction();
            } else {
                // Permission denied
                Toast.makeText(this, "Notification permission denied", Toast.LENGTH_SHORT).show();
                callNextFunction();
            }
        }
    }

    private void requestNotificationPermissionIfNeeded() {
        // Only for Android 13+ (API 33+)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            if (ContextCompat.checkSelfPermission(this,
                    Manifest.permission.POST_NOTIFICATIONS) != PackageManager.PERMISSION_GRANTED) {
                // Permission not granted → request it
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.POST_NOTIFICATIONS},
                        REQUEST_NOTIFICATION_PERMISSION);
            } else {
                // Permission already granted
                callNextFunction();
            }
        } else {
            // Permission not needed for < Android 13
            callNextFunction();
        }
    }

    private void callNextFunction(){
        try {
            myActivity = SplashActivity.this;

            final DisplayMetrics displayMetrics = new DisplayMetrics();
            getWindowManager().getDefaultDisplay().getMetrics(displayMetrics);
            setDeviceHeight(myActivity, displayMetrics.heightPixels);
            setDeviceWidth(myActivity, displayMetrics.widthPixels);
            final RelativeLayout rl_Star_Logo_splash = (RelativeLayout) findViewById(R.id.rl_Star_Logo_splash);
            rl_Star_Logo_splash.getLayoutParams().height = getDeviceHeight(myActivity);

            FirebaseMessaging.getInstance().getToken()
                    .addOnCompleteListener(new OnCompleteListener<String>() {
                        @Override
                        public void onComplete(@NonNull Task<String> task) {
                            Log.d("TAG", "onComplete: "+task.getResult());
                            if (!task.isSuccessful()) {
                                print_Log_d("Fetching FCM registration token failed", task.getException().toString());
                                set_firebase_token(myActivity, "dummy");
                                return;
                            }
                            set_firebase_token(myActivity, task.getResult());
                        }
                    });
            final String curr_date = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
            set_server_current_date(myActivity, curr_date);
            print_Log_d("Refreshed_token_s ", get_firebase_token(myActivity));
        } catch (final Exception e) {
            e.printStackTrace();
        } finally {
            nextScreen();
        }
    }

    private void nextScreen() {
        try {
            new Handler().postDelayed(new Runnable() {
                @Override
                public void run() {
                    if (getLoginStatus(myActivity)) {
                        if (get_user_type(myActivity).equalsIgnoreCase("TE")) {
                            startActivity(new Intent(getApplicationContext(), TeHomeActivity.class));
                        } else if (get_user_type(myActivity).equalsIgnoreCase("Engineer")) {
                            startActivity(new Intent(getApplicationContext(), EngineerHomeActivity.class));
                        } else {
                            startActivity(new Intent(getApplicationContext(), LoginStep1Activity.class));
                        }
                    } else {
                        startActivity(new Intent(getApplicationContext(), LoginStep1Activity.class));
                    }
                    finishAffinity();
                }
            }, 1900);
        } catch (final Exception e) {
            e.printStackTrace();
        } finally {
            setDeviceId_();
        }
    }

    public void setDeviceId_() {
        try {
            int val_banner_height = (int) (getDeviceHeight(myActivity) * .3);
            set_Banner_Height(myActivity, val_banner_height);
            int val_header_height = (int) (getDeviceHeight(myActivity) * .07);
            set_Header_Height(myActivity, val_header_height);

            if (SharedPrefData.getDeviceId(myActivity).matches("")) {
                final String device_id = new SimpleDateFormat("yyyyMMddHHmmssSSSSSSS",
                        Locale.getDefault()).format(new Date()) + "_" + System.currentTimeMillis();
                print_Log_d("device_id ", device_id);
                setDeviceId(myActivity, device_id);
            }
        } catch (SecurityException se) {
            se.printStackTrace();
        }
    }
}

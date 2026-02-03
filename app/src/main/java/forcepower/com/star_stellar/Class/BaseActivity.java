package forcepower.com.star_stellar.Class;

import android.graphics.Color;
import android.os.Build;
import android.os.Bundle;

import androidx.appcompat.app.AppCompatActivity;

import android.view.Window;
import android.view.WindowManager;

import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.activityGlobal;

public class BaseActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        try {
            activityGlobal = this;
            setStatusbar();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void setStatusbar() {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                Window window = getWindow();
                window.addFlags(WindowManager.LayoutParams.FLAG_DRAWS_SYSTEM_BAR_BACKGROUNDS);
                if (!statusBarColorCode.matches("") && statusBarColorCode.contains("#"))
                    window.setStatusBarColor(Color.parseColor(statusBarColorCode));
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

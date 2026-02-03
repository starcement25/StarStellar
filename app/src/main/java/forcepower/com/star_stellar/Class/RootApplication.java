package forcepower.com.star_stellar.Class;

import android.app.Application;
import android.content.Context;

import androidx.multidex.MultiDex;


/**
 * RootApplication is the base class within an Android app that contains all other components such as activities and services .
 */
public class RootApplication extends Application {
    @Override
    protected void attachBaseContext(Context base) {
        super.attachBaseContext(base);
        MultiDex.install(this);
    }
}

package forcepower.com.star_stellar.Class;

import android.content.Context;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.preference.PreferenceManager;

/**
 * This class will store the state of the user whether he has voted or not.
 */

public class SharedPrefData {

    static final String KEY_SET_login_status = "KEY_SET_login_status";
    static final String KEY_SET_DeviceHeight = "KEY_SET_DeviceHeight";
    static final String KEY_SET_DeviceWidth = "KEY_SET_DeviceWidth";
    static final String KEY_SET_TE_login_JSON_data = "KEY_SET_TE_login_JSON_data";
    static final String KEY_SET_TE_name = "KEY_SET_TE_name";
    static final String KEY_SET_TE_id = "KEY_SET_TE_id";
    static final String KEY_SET_TE_mobile = "KEY_SET_TE_mobile";
    static final String KEY_SET_TE_code = "KEY_SET_TE_code";
    static final String KEY_SET_TE_email = "KEY_SET_TE_email";
    static final String KEY_SET_e_profile_image = "KEY_SET_e_profile_image";
    static final String KEY_SET_e_dob = "KEY_SET_e_dob";
    static final String KEY_SET_e_dom = "KEY_SET_e_dom";
    static final String KEY_SET_e_address = "KEY_SET_e_address";
    static final String KEY_SET_e_pin = "KEY_SET_e_pin";
    static final String KEY_SET_e_state = "KEY_SET_e_state";
    static final String KEY_SET_e_city_town = "KEY_SET_e_city_town";
    static final String KEY_SET_DeviceID = "KEY_SET_DeviceID";
    static final String KEY_SET_Banner_Height = "KEY_SET_Banner_Height";
    static final String KEY_SET_Header_Height = "KEY_SET_Header_Height";
    static final String KEY_SET_user_type = "KEY_SET_user_type";
    static final String KEY_SET_E_email = "KEY_SET_E_email";
    static final String KEY_SET_E_code = "KEY_SET_E_code";
    static final String KEY_SET_E_mobile = "KEY_SET_E_mobile";
    static final String KEY_SET_E_name = "KEY_SET_E_name";
    static final String KEY_SET_E_id = "KEY_SET_E_id";
    static final String KEY_SET_E_login_JSON_data = "KEY_SET_E_login_JSON_data";
    static final String KEY_SET_profile_data = "KEY_SET_profile_data";
    static final String KEY_SET_firebase_token = "KEY_SET_firebase_token";
    static final String KEY_SET_E_points = "KEY_SET_E_points";
    static final String KEY_SET_TE_points = "KEY_SET_TE_points";
    static final String KEY_SET_E_points_msg = "KEY_SET_E_points_msg";
    static final String KEY_SET_current_date = "KEY_SET_current_date";

    public static SharedPreferences getSharedPreferences(final Context ctx) {
        return PreferenceManager.getDefaultSharedPreferences(ctx);
    }

    public static void setLoginStatus(final Context ctx, final boolean status) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putBoolean(KEY_SET_login_status, status);
        editor.apply();
    }

    public static boolean getLoginStatus(final Context ctx) {
        return getSharedPreferences(ctx).getBoolean(KEY_SET_login_status, false);
    }

    public static void setDeviceHeight(final Context ctx, final int DeviceHeight) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putInt(KEY_SET_DeviceHeight, DeviceHeight);
        editor.apply();
    }

    public static int getDeviceHeight(final Context ctx) {
        return getSharedPreferences(ctx).getInt(KEY_SET_DeviceHeight, 600);
    }

    public static void set_Banner_Height(final Context ctx, final int Banner_Height) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putInt(KEY_SET_Banner_Height, Banner_Height);
        editor.apply();
    }

    public static int get_Banner_Height(final Context ctx) {
        return getSharedPreferences(ctx).getInt(KEY_SET_Banner_Height, 100);
    }

    public static void set_Header_Height(final Context ctx, final int Banner_Height) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putInt(KEY_SET_Header_Height, Banner_Height);
        editor.apply();
    }

    public static int get_Header_Height(final Context ctx) {
        return getSharedPreferences(ctx).getInt(KEY_SET_Header_Height, 180);
    }

    public static void setDeviceWidth(final Context ctx, final int DeviceWidth) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putInt(KEY_SET_DeviceWidth, DeviceWidth);
        editor.apply();
    }

    public static int getDeviceWidth(final Context ctx) {
        return getSharedPreferences(ctx).getInt(KEY_SET_DeviceWidth, 300);
    }

    public static void set_TE_loginJsonData(final Context ctx, final String LoginJsonData) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_login_JSON_data, LoginJsonData);
        editor.apply();
    }

    public static void set_E_loginJsonData(final Context ctx, final String LoginJsonData) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_login_JSON_data, LoginJsonData);
        editor.apply();
    }

    public static String get_TE_loginJsonData(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_login_JSON_data, "");
    }

    public static void set_TE_id(final Context ctx, final String the_engineer_id) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_id, the_engineer_id);
        editor.apply();
    }

    public static void set_E_id(final Context ctx, final String the_engineer_id) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_id, the_engineer_id);
        editor.apply();
    }

    public static String get_E_id(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_E_id, "");
    }

    public static String get_TE_id(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_id, "");
    }

    public static void set_TE_name(final Context ctx, final String e_name) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_name, e_name);
        editor.apply();
    }

    public static void set_E_name(final Context ctx, final String e_name) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_name, e_name + "".trim());
        editor.apply();
    }

    public static String get_E_name(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_E_name, "");
    }

    public static void set_E_points_msg(final Context ctx, final String e_name) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_points_msg, e_name + "".trim());
        editor.apply();
    }

    public static String get_E_points_msg(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_E_points_msg, "Stellar Points : ");
    }

    public static void set_E_points(final Context ctx, final String points) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_points, points);
        editor.apply();
    }

    public static void set_TE_points(final Context ctx, final String points) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_points, points);
        editor.apply();
    }

    public static String get_TE_points(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_points, "");
    }

    public static String get_E_points(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_E_points, "");
    }

    public static String get_TE_name(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_name, "");
    }

    public static void set_TE_mobile(final Context ctx, final String e_mobile) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_mobile, e_mobile);
        editor.apply();
    }

    public static void set_E_mobile(final Context ctx, final String e_mobile) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_mobile, e_mobile);
        editor.apply();
    }

    public static String get_TE_mobile(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_mobile, "");
    }

    public static void set_TE_code(final Context ctx, final String te_code) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_code, te_code);
        editor.apply();
    }

    public static void set_E_code(final Context ctx, final String te_code) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_code, te_code);
        editor.apply();
    }

    public static String get_E_code(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_E_code, "");
    }

    public static String get_TE_code(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_code, "");
    }

    public static void set_TE_email(final Context ctx, final String e_email) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_TE_email, e_email);
        editor.apply();
    }

    public static void set_E_email(final Context ctx, final String e_email) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_E_email, e_email);
        editor.apply();
    }

    public static String get_TE_email(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_TE_email, "");
    }

    public static void set_e_profile_image(final Context ctx, final String e_profile_image) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_profile_image, e_profile_image);
        editor.apply();
    }

    public static void set_E_city(final Context ctx, final String e_profile_image) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_city_town, e_profile_image);
        editor.apply();
    }

    public static void set_E_state(final Context ctx, final String e_profile_image) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_state, e_profile_image);
        editor.apply();
    }

    public static void set_E_pin(final Context ctx, final String e_profile_image) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_pin, e_profile_image);
        editor.apply();
    }

    public static void set_E_address(final Context ctx, final String e_profile_image) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_address, e_profile_image);
        editor.apply();
    }

    public static void set_ALL(final Context ctx, final String e_dob, final String e_dom, final String e_address,
                               String e_pin, final String e_state, final String e_city_town) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_e_dob, e_dob);
        editor.putString(KEY_SET_e_dom, e_dom);
        editor.putString(KEY_SET_e_address, e_address);
        editor.putString(KEY_SET_e_pin, e_pin);
        editor.putString(KEY_SET_e_state, e_state);
        editor.putString(KEY_SET_e_city_town, e_city_town);
        editor.apply();
    }

    public static String get_E_address(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_address, "");
    }

    public static String get_E_city(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_city_town, "");
    }

    public static String get_E_pin(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_pin, "");
    }

    public static String get_E_state(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_state, "");
    }

    public static String get_E_dob(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_dob, "");
    }

    public static String get_E_dom(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_dom, "");
    }

    public static String get_E_profile_image(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_profile_image, "");
    }

    public static String get_e_profile_image(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_e_profile_image, "");
    }

    public static void setDeviceId(final Context ctx, final String memberid) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_DeviceID, memberid);
        editor.apply();
    }

    public static String getDeviceId(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_DeviceID, "");
    }

    public static void set_user_type(final Context ctx, final String user_type) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_user_type, user_type);
        editor.apply();
    }

    public static String get_user_type(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_user_type, "");
    }

    public static void set_profile_data(final Context ctx, final String profile_data) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_profile_data, profile_data);
        editor.apply();
    }

    public static String get_profile_data(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_profile_data, "");
    }

    public static void set_firebase_token(final Context ctx, final String firebase_token) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_firebase_token, firebase_token);
        editor.apply();
    }

    public static String get_firebase_token(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_firebase_token, "dummy");
    }

    //
    public static void set_logout(final Context ctx) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.clear();
        editor.apply();
    }


    public static String get_server_current_date(final Context ctx) {
        return getSharedPreferences(ctx).getString(KEY_SET_current_date, "0000-00-00"); //yyyy-MM-dd
    }

    public static void set_server_current_date(final Context ctx, final String v) {
        final Editor editor = getSharedPreferences(ctx).edit();
        editor.putString(KEY_SET_current_date, v);
        editor.apply();
    }
}

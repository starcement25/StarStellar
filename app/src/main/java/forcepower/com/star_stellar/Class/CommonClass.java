package forcepower.com.star_stellar.Class;

import android.app.Activity;
import android.app.Dialog;
import android.content.Context;
import android.net.ConnectivityManager;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.inputmethod.InputMethodManager;
import android.widget.EditText;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.Locale;
import java.util.regex.Pattern;

import forcepower.com.star_stellar.BuildConfig;
import forcepower.com.star_stellar.R;

public class CommonClass {
    public static Activity activityGlobal;
    public static String statusBarColorCode = "#bb1508",
            defaultColorCode = "#d22a1d", checkInternetConnection = "Please check internet connection.";

    public static final int REQUEST_IMAGE = 100, DEFAULT_TIMEOUT = 20 * 1000;
    public static boolean reload = false;
    public static String contact_person_categories = "", product_data = "";
    public static String user_type = "", mobile = "", profile_JSON_data = "";

    public static void hideKeyboard(final Activity activity) {
        final InputMethodManager imm = (InputMethodManager) activity.getSystemService(Activity.INPUT_METHOD_SERVICE);
        //Find the currently focused view, so we can grab the correct window token from it.
        View view = activity.getCurrentFocus();
        //If no view currently has focus, create a new one, just so we can grab a window token from it
        if (view == null) {
            view = new View(activity);
        }
        imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
    }

    public static void showKeyboard(final Activity activity, final EditText editText) {
        final InputMethodManager imm = (InputMethodManager) activity.getSystemService(Context.INPUT_METHOD_SERVICE);
        editText.requestFocus();
        imm.showSoftInput(editText, InputMethodManager.SHOW_IMPLICIT);
    }

    public static void print_Log_d(final String key, final String val) {
        if (BuildConfig.DEBUG)
            Log.d(key, val);
    }

    public static boolean isInternetConnected(final Activity context) {
        try {
            final ConnectivityManager cm = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
            return cm.getActiveNetworkInfo() != null;
        } catch (final Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    public static final Pattern EMAIL_ADDRESS_PATTERN = Pattern.compile(
            "^(([\\w-]+\\.)+[\\w-]+|([a-zA-Z]{1}|[\\w-]{2,}))@"
                    + "((([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
                    + "[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\."
                    + "([0-1]?[0-9]{1,2}|25[0-5]|2[0-4][0-9])\\.([0-1]?"
                    + "[0-9]{1,2}|25[0-5]|2[0-4][0-9])){1}|"
                    + "([a-zA-Z]+[\\w-]+\\.)+[a-zA-Z]{2,4})$"
    );

    public static boolean validEmailFormat(final String UserEmailNo) {
        return EMAIL_ADDRESS_PATTERN.matcher(UserEmailNo).matches();
    }

    public static boolean isValidMobile(final String phone) {
        if (!Pattern.matches("[a-zA-Z]+", phone)) {
            return phone.matches("[6-9][0-9]{9}");
        }
        return false;
    }

    public static boolean isValidPinCode(final String phone) {
        if (!Pattern.matches("[a-zA-Z]+", phone)) {
            return phone.matches("[1-9][0-9]{5}");
        }
        return false;
    }

    public static void setListViewHeightBasedOnItems(final ListView listview) {
        try {
            final ListAdapter listadp = listview.getAdapter();
            if (listadp != null) {
                int totalHeight = 35;
                for (int i = 0; i < listadp.getCount(); i++) {
                    View listItem = listadp.getView(i, null, listview);
                    listItem.measure(0, listItem.getHeight());
                    totalHeight += listItem.getMeasuredHeight();
                }
                ViewGroup.LayoutParams params = listview.getLayoutParams();
                params.height = totalHeight + (listview.getDividerHeight() * (listadp.getCount() - 1));
                listview.setLayoutParams(params);
                listview.requestLayout();
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public static void msg_Dialog(final Activity myActivity, final String msg, final boolean finishAble) {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            final TextView tvCPopup = new TextView(myActivity);
            tvCPopup.setText(myActivity.getResources().getString(R.string.app_name));
            tvCPopup.setGravity(Gravity.CENTER);
            tvCPopup.setTextColor(myActivity.getResources().getColor(R.color.white));
            tvCPopup.setTextSize(12);
            tvCPopup.setBackgroundColor(myActivity.getResources().getColor(R.color.red));
            int margin = 15;
            tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
            issueBuilder.setCustomTitle(tvCPopup);

            // Get the layout inflater
            final LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_mail, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(!finishAble);
            dialog.setCancelable(!finishAble);
            dialog.show();

            final TextView tv_submit = (TextView) dialog.findViewById(R.id.tv_submit);
            final TextView tv_msg = (TextView) dialog.findViewById(R.id.tv_msg);
            tv_msg.setText(msg + "");
            tv_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    try {
                        dialog.dismiss();

                        if (finishAble)
                            myActivity.finish();
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public static String changeDateFormat_(final String inputDateString,
                                           final String inputDateFormat,
                                           final String outputDateFormat) {
        final SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat, Locale.getDefault());
        final SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat, Locale.getDefault());

        Date date = null;
        String outputDateString = null;

        try {
            date = inputFormat.parse(inputDateString);
            outputDateString = outputFormat.format(date);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return outputDateString;
    }

    public static String changeDateFormat(String inputDateFormat, String outputDateFormat, String inputDateString) {
        String outputDateString = "";
        try {
            final SimpleDateFormat inputFormat = new SimpleDateFormat(inputDateFormat, Locale.getDefault());
            final SimpleDateFormat outputFormat = new SimpleDateFormat(outputDateFormat, Locale.getDefault());

            Date date = inputFormat.parse(inputDateString);
            outputDateString = outputFormat.format(date);
        } catch (ParseException e) {
            e.printStackTrace();
        }
        return outputDateString;
    }

    public static long dateToMilliSecond(final String mDate) {
        long millis = 0;
        try {
            final SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault());
            final Date date = sdf.parse(mDate);
            millis = date.getTime();
        } catch (Exception e) {
            e.printStackTrace();
        }
        return millis;
    }
}

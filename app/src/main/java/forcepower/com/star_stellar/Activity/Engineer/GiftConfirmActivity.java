package forcepower.com.star_stellar.Activity.Engineer;

import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.os.Bundle;

import androidx.appcompat.app.AlertDialog;

import android.view.LayoutInflater;
import android.view.View;
import android.view.Window;
import android.widget.CheckBox;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_make_gift_order;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_address;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_city;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_pin;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_state;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_address;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_city;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_pin;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_points;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_points_msg;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_state;

public class GiftConfirmActivity extends BaseActivity {
    Activity myActivity;
    EditText et_gift_address, et_gift_city, et_gift_pin, et_gift_state;
    CheckBox cb_gift_TC, cb_gift_default;
    String gift_id = "";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gift_confirm);

        try {
            myActivity = GiftConfirmActivity.this;
            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Confirm");
            ImageView iv_gift_item = (ImageView) findViewById(R.id.iv_gift_item);
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            gift_id = getIntent().getStringExtra("gift_id"); //gift_id
            String gift_title = getIntent().getStringExtra("gift_title"); //gift_title
            String gift_description = getIntent().getStringExtra("gift_description"); //gift_description
            String gift_image_url = getIntent().getStringExtra("gift_image_url"); //gift_image_url
            int point_require = Integer.parseInt(getIntent().getStringExtra("point_require")); //point_require
            String point_require_text = getIntent().getStringExtra("point_require_text"); //point_require_text
            String button_status = getIntent().getStringExtra("button_status"); //button_status
            int e_points = Integer.parseInt(getIntent().getStringExtra("e_points")); //e_points

            //gift_image_url
            Glide.with(myActivity).load(gift_image_url)
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .dontAnimate()
                    .error(R.drawable.default_)
                    .into(iv_gift_item);

            TextView tv_git_title = (TextView) findViewById(R.id.tv_git_title);
            tv_git_title.setText(gift_title);

            TextView tv_gift_my_points = (TextView) findViewById(R.id.tv_gift_my_points);
            tv_gift_my_points.setText("" + e_points);

            TextView tv_gift_points = (TextView) findViewById(R.id.tv_gift_points);
            tv_gift_points.setText("Points- " + point_require);

            TextView tv_gift_require_points = (TextView) findViewById(R.id.tv_gift_require_points);
            tv_gift_require_points.setText("" + point_require);

            TextView tv_gift_bal_points = (TextView) findViewById(R.id.tv_gift_bal_points);
            tv_gift_bal_points.setText("" + (e_points - point_require));


            et_gift_address = (EditText) findViewById(R.id.et_gift_address);
            et_gift_city = (EditText) findViewById(R.id.et_gift_city);
            et_gift_pin = (EditText) findViewById(R.id.et_gift_pin);
            et_gift_state = (EditText) findViewById(R.id.et_gift_state);

            et_gift_address.setText(get_E_address(myActivity));
            et_gift_address.setSelection(et_gift_address.getText().toString().trim().length());
            et_gift_city.setText(get_E_city(myActivity));
            et_gift_pin.setText(get_E_pin(myActivity));
            et_gift_state.setText(get_E_state(myActivity));

            cb_gift_TC = (CheckBox) findViewById(R.id.cb_gift_TC);
            cb_gift_default = (CheckBox) findViewById(R.id.cb_gift_default);

        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void gift_confirmed_dialog(String msg) {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");

            builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    Intent intent = new Intent(myActivity, EngineerHomeActivity.class);
                    startActivity(intent);
                    finishAffinity();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(false);
            dialog.setCanceledOnTouchOutside(false);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void gift_back_dialog() {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage("Do you want to cancel the Redemption?");

            builder.setNegativeButton("No", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    //
                }
            });
            builder.setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {
                    finish();
                }
            });
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        gift_back_dialog();
    }

    public void gift_confirm(View view) {
        try {
            if (!cb_gift_TC.isChecked()) {
                Toast.makeText(myActivity, "Agree to the T&C", Toast.LENGTH_SHORT).show();
            } else if (et_gift_address.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter address", Toast.LENGTH_SHORT).show();
            } else if (et_gift_city.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter city name", Toast.LENGTH_SHORT).show();
            } else if (et_gift_state.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter state name", Toast.LENGTH_SHORT).show();
            } else if (et_gift_pin.getText().toString().trim().length() != 6 ||
                    et_gift_pin.getText().toString().startsWith("0")) {
                Toast.makeText(myActivity, "Please enter a valid pin code", Toast.LENGTH_SHORT).show();
            } else if (!isInternetConnected(myActivity)) {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            } else {
                String address = et_gift_address.getText().toString();
                String city = et_gift_city.getText().toString();
                String pin = et_gift_pin.getText().toString();
                String state = et_gift_state.getText().toString();
                if (cb_gift_default.isChecked()) {
                    continueSubmitGift(address, city, pin, state, "YES");
                } else {
                    continueSubmitGift(address, city, pin, state, "NO");
                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void continueSubmitGift(final String address, final String city, final String pin, final String state,
                                   String default_address) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("gift_id", gift_id);
        params.put("e_address", address);
        params.put("e_city", city);
        params.put("e_pin", pin);
        params.put("e_state", state);
        params.put("set_as_default_profile_address", default_address);
/*
gift_id,the_engineer_id,set_as_default_profile_address,e_address,e_city,e_pin,e_state

Note: set_as_default_profile_address = YES or NO
if set_as_default_profile_address = YES then send values in e_address,e_city,e_pin,e_state
 */
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_make_gift_order, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("gift_submit", str + "");
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        if (cb_gift_default.isChecked()) {
                            set_E_address(myActivity, address);
                            set_E_city(myActivity, city);
                            set_E_pin(myActivity, pin);
                            set_E_state(myActivity, state);
                            set_E_points(myActivity, reader.optString("the_point"));
                            set_E_points_msg(myActivity, reader.optString("e_points_msg"));
                        }
                        gift_confirmed_dialog(reader.optString("process_message"));
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

    public void TC_dialog(View view) {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_tc, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
            dialog.show();

            final TextView tv_cross = (TextView) dialog.findViewById(R.id.tv_cross);
            tv_cross.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    dialog.dismiss();
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

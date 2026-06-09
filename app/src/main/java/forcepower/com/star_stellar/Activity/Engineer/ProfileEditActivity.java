package forcepower.com.star_stellar.Activity.Engineer;

import android.Manifest;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Color;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;

import static forcepower.com.star_stellar.Class.CommonClass.REQUEST_IMAGE;

import androidx.appcompat.app.AlertDialog;

import android.view.Gravity;
import android.view.View;
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

import org.json.JSONArray;
import org.json.JSONObject;

import java.io.File;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.Class.MarshMallowPermission;
import forcepower.com.star_stellar.R;
import my_crop.vola.ImagePickerActivity;

import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.AllUrl.ws_update_profile_details_for_engineer;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.hideKeyboard;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.isValidPinCode;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.CommonClass.profile_JSON_data;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.MarshMallowPermission.CAMERA_PERMISSION_REQUEST_CODE;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceHeight;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class ProfileEditActivity extends BaseActivity {
    public EditText et_P_birthday, et_P_anniversary;
    EditText et_P_city_town, et_P_state, et_P_pincode, et_P_address, et_P_name;
    Activity myActivity;
    ImageView iv_Star_Logo_Cam, iv_Star_Logo_F;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile_edit);
        try {
            myActivity = ProfileEditActivity.this;

            RelativeLayout rl_Star_Logo_P = (RelativeLayout) findViewById(R.id.rl_Star_Logo_P);
            double val = (int) getDeviceHeight(myActivity) * .3;
            rl_Star_Logo_P.getLayoutParams().height = (int) val;

            et_P_name = (EditText) findViewById(R.id.et_P_name);
            et_P_birthday = (EditText) findViewById(R.id.et_P_birthday);
            et_P_anniversary = (EditText) findViewById(R.id.et_P_anniversary);
            et_P_address = (EditText) findViewById(R.id.et_P_address);
            et_P_pincode = (EditText) findViewById(R.id.et_P_pincode);
            et_P_state = (EditText) findViewById(R.id.et_P_state);
            et_P_city_town = (EditText) findViewById(R.id.et_P_city_town);


            et_P_birthday.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
//                    DialogFragment newFragment = new DatePickerFragment();
//                    newFragment.show(getFragmentManager(), "et_P_birthday");
                }
            });
            et_P_anniversary.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
//                    DialogFragment newFragment = new DatePickerFragment();
//                    newFragment.show(getFragmentManager(), "et_P_anniversary");
                }
            });

            //Header_View
            RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));
            LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);
            RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);
            TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Edit Profile");
            ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });

            iv_Star_Logo_F = (ImageView) findViewById(R.id.iv_Star_Logo_F);
            iv_Star_Logo_Cam = (ImageView) findViewById(R.id.iv_Star_Logo_Cam);
            //
            if (!profile_JSON_data.matches("")) {
                parse_profile_json_data(profile_JSON_data);
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    /*
    public static class DatePickerFragment extends DialogFragment implements DatePickerDialog.OnDateSetListener
    {
        String tag="";
        @Override
        public Dialog onCreateDialog(Bundle savedInstanceState)
        {
            tag =getTag()+"";
            final Calendar c = Calendar.getInstance();
            int year = 0, month = 0, day = 0;
            try
            {
                String setDialogDate ="";
                if(tag.matches("et_P_birthday"))
                {
                    setDialogDate = et_P_birthday.getText().toString()+"";
                }
                else if(tag.matches("et_P_anniversary"))
                {
                    setDialogDate = et_P_anniversary.getText().toString()+"";
                }
                if(!setDialogDate.equalsIgnoreCase("") && setDialogDate.contains("-"))
                {
                    String [] ddMMyyyy = setDialogDate.split("-");
                    if(ddMMyyyy.length == 3)
                    {
                        day = Integer.parseInt(ddMMyyyy[2]);
                        month = Integer.parseInt(ddMMyyyy[1]) - 1;
                        year = Integer.parseInt(ddMMyyyy[0]);
                    }
                }
                else
                {
                    year = c.get(Calendar.YEAR);
                    month = c.get(Calendar.MONTH);
                    day = c.get(Calendar.DAY_OF_MONTH);
                }
            }
            catch (final Exception e)
            {
                e.printStackTrace();
            }

            DatePickerDialog dialog = new DatePickerDialog(getActivity(), this, year, month, day);
//            dialog.getDatePicker().setMaxDate(c.getTimeInMillis());
            return  dialog;
        }

        public void onDateSet(DatePicker view, int year, int month, int day)
        {
            String sDay = ""+day, sMondth = ""+(month + 1);
            if(sDay.length() == 1)
            {
                sDay = "0"+sDay;
            }
            if(sMondth.length() == 1)
            {
                sMondth = "0"+sMondth;
            }

            String date = year +"-"+ sMondth + "-" +  sDay;
            if(tag.matches("et_P_birthday"))
            {
                et_P_birthday.setText(date);
            }
            else if(tag.matches("et_P_anniversary"))
            {
                et_P_anniversary.setText(date);
            }
        }
    }
    */
    private static final int CAMERA_CODE = 101, GALLERY_CODE = 201, CROPPING_CODE = 301;
    MarshMallowPermission mMP;
    File outPutFile = new File("");

    public void chooseYourImage_P(View view) {
        //for Runtime permission
        mMP = new MarshMallowPermission(myActivity);
        final CharSequence[] items = {"Camera", "Gallery"};

        AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);

        final TextView tvCPopup = new TextView(myActivity);
        tvCPopup.setText("Upload profile image");
        tvCPopup.setGravity(Gravity.CENTER);
        tvCPopup.setTextColor(getResources().getColor(R.color.white));
        tvCPopup.setTextSize(12);
        tvCPopup.setBackgroundColor(getResources().getColor(R.color.red));
        int margin = 15;
        tvCPopup.setPadding(0, margin * 2, 0, margin * 2);
        builder.setCustomTitle(tvCPopup);

        builder.setItems(items, new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int item) {

                if (items[item].equals("Camera")) {

                    if (!mMP.checkPermissionForCamera()) {
                        mMP.requestPermissionForCamera();
                    } else {
                        openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
                    }

                } else if (items[item].equals("Gallery")) {
                    openCamera(ImagePickerActivity.REQUEST_GALLERY_IMAGE);
                }
            }
        }).setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
            @Override
            public void onClick(DialogInterface dialog, int which) {
                //
            }
        });
        builder.show();
    }

    public void openCamera(final int codeeee) {
        try {
            Intent intent = new Intent(this, ImagePickerActivity.class);
            intent.putExtra(ImagePickerActivity.INTENT_IMAGE_PICKER_OPTION, codeeee);

            // setting aspect ratio
            intent.putExtra(ImagePickerActivity.INTENT_LOCK_ASPECT_RATIO, true);
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_X, 1); // 16x9, 1x1, 3:4, 3:2
            intent.putExtra(ImagePickerActivity.INTENT_ASPECT_RATIO_Y, 1);

            // setting maximum bitmap width and height
            intent.putExtra(ImagePickerActivity.INTENT_SET_BITMAP_MAX_WIDTH_HEIGHT, true);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_WIDTH, 1000);
            intent.putExtra(ImagePickerActivity.INTENT_BITMAP_MAX_HEIGHT, 1000);

            myActivity.startActivityForResult(intent, REQUEST_IMAGE);
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void requestPermission() {
        ActivityCompat.requestPermissions(this, new String[]{
                Manifest.permission.CAMERA
        }, CAMERA_PERMISSION_REQUEST_CODE);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        switch (requestCode) {
            case CAMERA_PERMISSION_REQUEST_CODE:
                try {
                    if (!mMP.checkPermissionForCamera()) {
                        requestPermission();
                    } else {
                        openCamera(ImagePickerActivity.REQUEST_IMAGE_CAPTURE);
                    }
                } catch (final Exception e) {
                    e.printStackTrace();
                }
                break;
        }
    }

    @Override
    protected void onActivityResult(int requestCode, int resultCode, @Nullable Intent data) {
        super.onActivityResult(requestCode, resultCode, data);
        if (requestCode == REQUEST_IMAGE) {
            if (resultCode == Activity.RESULT_OK) {
                try {
                    assert data != null;
                    final Uri uri = data.getParcelableExtra("path");
                    iv_Star_Logo_F.setImageURI(uri);
                    iv_Star_Logo_Cam.setVisibility(View.INVISIBLE);
                    outPutFile = new File(uri.getPath());
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        }
    }

    private void parse_profile_json_data(String str) {
        try {
            final JSONObject reader = new JSONObject(str);
            if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                et_P_name.setText(reader.optString("e_name"));
                et_P_name.setSelection(et_P_name.getText().toString().trim().length());

                String e_profile_image = reader.optString("e_profile_image");
                Glide.with(myActivity).load(e_profile_image)
                        .diskCacheStrategy(DiskCacheStrategy.NONE)
                        .skipMemoryCache(true)
                        .dontAnimate()
                        .into(iv_Star_Logo_F);

                String profile_data = reader.optString("profile_data");
                JSONArray ja = new JSONArray(profile_data);
                if (ja.length() > 0) {

                    for (int i = 0; i < ja.length(); i++) {
                        final JSONObject e = ja.getJSONObject(i);

                        if (e.optString("label").matches("Address")) {
                            et_P_address.setText(e.optString("value"));
                        } else if (e.optString("label").matches("City/Town")) {
                            et_P_city_town.setText(e.optString("value"));
                        } else if (e.optString("label").matches("Pin")) {
                            et_P_pincode.setText(e.optString("value"));
                        } else if (e.optString("label").matches("State")) {
                            et_P_state.setText(e.optString("value"));
                        } else if (e.optString("label").matches("Birthday")) {
                            et_P_birthday.setText(e.optString("value"));
                        } else if (e.optString("label").matches("Anniversary")) {
                            et_P_anniversary.setText(e.optString("value"));
                        }

                    }

                }
            }
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void update_profile(View view) {
        try {
            String val = et_P_pincode.getText().toString().trim() + "";
            if (et_P_name.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter name", Toast.LENGTH_SHORT).show();
                et_P_name.requestFocus();
            } else if (et_P_address.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter address", Toast.LENGTH_SHORT).show();
                et_P_address.requestFocus();
            } else if (!isValidPinCode(val)) {
                Toast.makeText(myActivity, "Please enter 6 digit pin code", Toast.LENGTH_SHORT).show();
                et_P_pincode.requestFocus();
            } else if (et_P_state.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter state name", Toast.LENGTH_SHORT).show();
                et_P_state.requestFocus();
            } else if (et_P_city_town.getText().toString().trim().matches("")) {
                Toast.makeText(myActivity, "Please enter city/town name", Toast.LENGTH_SHORT).show();
                et_P_city_town.requestFocus();
            } else if (!isInternetConnected(myActivity)) {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            } else {
                continue_update_profile();
                hideKeyboard(myActivity);
            }


        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void continue_update_profile() {
        try {
            loadDialog();

            final RequestParams params = new RequestParams();

            params.put("the_engineer_id", get_E_id(myActivity));
            params.put("e_name", et_P_name.getText().toString().trim());
            params.put("e_dob", et_P_birthday.getText().toString().trim());
            params.put("e_dom", et_P_anniversary.getText().toString().trim());
            params.put("e_address", et_P_address.getText().toString().trim());
            params.put("e_pin", et_P_pincode.getText().toString().trim());
            params.put("e_state", et_P_state.getText().toString().trim());
            params.put("e_city_town", et_P_city_town.getText().toString().trim());

            print_Log_d("profile_file_path", outPutFile.getPath());
            if (outPutFile.exists()) {
                params.put("e_profile_image", outPutFile, "image/jpeg");
            }

            final AsyncHttpClient client = new AsyncHttpClient();
            client.setTimeout(DEFAULT_TIMEOUT);
            client.post(ws_update_profile_details_for_engineer, params, new AsyncHttpResponseHandler() {
                @Override
                public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                    final String str = new String(responseBody);
                    try {
                        final JSONObject reader = new JSONObject(str);
                        print_Log_d("profile_update_submitted ", str + "");

                        Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                        if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                            onBackPressed();
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
        } catch (final Exception e) {
            e.printStackTrace();
        }
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
}


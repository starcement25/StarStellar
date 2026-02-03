package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.graphics.Paint;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;
import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import java.util.ArrayList;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.MyOrdersActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.ws_submit_support_with_respect_to_order;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;


public class MyOrderDeliveredAdapter extends BaseAdapter {

    private Activity myActivity;
    private ArrayList<CommonHelper> p_item_list = new ArrayList<>();

    public MyOrderDeliveredAdapter(Activity myActivity, ArrayList<CommonHelper> p_item_list_) {
        this.myActivity = myActivity;
        this.p_item_list = p_item_list_;
    }

    @Override
    public int getCount() {
        return p_item_list.size();
    }

    @Override
    public CommonHelper getItem(int position) {

        return p_item_list.get(position);
    }

    @Override
    public long getItemId(int position) {

        return 0;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        ViewHolder holder = null;
        final LayoutInflater mInflater = (LayoutInflater) myActivity
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_my_order_delivered, null);
            holder = new ViewHolder();

            holder.tv_P_item = (TextView) convertView.findViewById(R.id.tv_P_item);
            holder.tv_P_details = (TextView) convertView.findViewById(R.id.tv_P_details);
            holder.iv_gift_item = (ImageView) convertView.findViewById(R.id.iv_gift_item);
            holder.tv_P_order_id = (TextView) convertView.findViewById(R.id.tv_P_order_id);
            holder.tv_order_del_date = (TextView) convertView.findViewById(R.id.tv_order_del_date);
            holder.tv_json_row = (TextView) convertView.findViewById(R.id.tv_json_row);
            holder.tv_support = (TextView) convertView.findViewById(R.id.tv_support);
            holder.tv_gift_delivered = (TextView) convertView.findViewById(R.id.tv_gift_delivered);

            holder.tv_p_TYPE = (TextView) convertView.findViewById(R.id.tv_p_TYPE);
            holder.tv_p_Comments = (TextView) convertView.findViewById(R.id.tv_p_Comments);
            holder.tv_p_Status = (TextView) convertView.findViewById(R.id.tv_p_Status);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_support.setText("SUPPORT");
        holder.tv_support.setPaintFlags(holder.tv_support.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
        holder.tv_support.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                order_help_dialog(p_item_list.get(position).getItem5());
            }
        });
        Glide.with(myActivity).load(p_item_list.get(position).getItem1())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .dontAnimate()
                .into(holder.iv_gift_item);
        holder.tv_P_item.setText(p_item_list.get(position).getItem0());
        holder.tv_P_details.setText(p_item_list.get(position).getItem4()); //point_taken_text
        holder.tv_P_details.setPadding(5, 0, 5, 0);
        holder.tv_P_order_id.setText("Order id : " + p_item_list.get(position).getItem5()); //order_id

        holder.tv_p_TYPE.setText(p_item_list.get(position).getItem8());
        holder.tv_p_Comments.setText(p_item_list.get(position).getItem9());
        holder.tv_p_Status.setText(p_item_list.get(position).getItem2());

        if (!p_item_list.get(position).getItem6().matches("")) {
            holder.tv_order_del_date.setText("Delivery Date : " + p_item_list.get(position).getItem6()); //delivery_date

        } else {
            holder.tv_order_del_date.setText("Delivery Date : To be Updated"); //delivery_date
        }

        holder.tv_json_row.setText(p_item_list.get(position).getItem3());

        if (p_item_list.get(position).getItem7().equalsIgnoreCase("Yes")) {
            holder.tv_gift_delivered.setVisibility(View.GONE);
        } else {
            holder.tv_gift_delivered.setVisibility(View.VISIBLE);
        }
        holder.tv_gift_delivered.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                order_Delivered(p_item_list.get(position).getItem5(), p_item_list.get(position).getItem7());

            }
        });

        return convertView;
    }

    public void setFilter(ArrayList<CommonHelper> approved_list, String type) {
        if (type.matches("fresh_a")) {
            this.p_item_list.clear();
        }
        this.p_item_list.addAll(approved_list);
    }

    public class ViewHolder {
        TextView tv_P_item, tv_P_details, tv_P_order_id, tv_order_del_date,
                tv_json_row, tv_support, tv_gift_delivered, tv_p_TYPE, tv_p_Comments, tv_p_Status;
        ImageView iv_gift_item;
    }

    String temp__ = "YES";

    private void order_Delivered(final String order_id, final String is_order_received) {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_order_help, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
//			dialog.getWindow().getAttributes().windowAnimations = R.style.DialogAnimation;
//			Window window = dialog.getWindow();
            dialog.show();

            final EditText et_gift_comments = (EditText) dialog.findViewById(R.id.et_gift_comments);
            et_gift_comments.setVisibility(View.GONE);
            final RadioGroup rg_gift_help = (RadioGroup) dialog.findViewById(R.id.rg_gift_help);

            final RadioButton rb_0 = (RadioButton) dialog.findViewById(R.id.rb_0);
            rb_0.setText("Yes");

            final RadioButton rb_1 = (RadioButton) dialog.findViewById(R.id.rb_1);
            rb_1.setText("No");
            temp__ = "YES";
            final RadioButton rb_2 = (RadioButton) dialog.findViewById(R.id.rb_2);
            rb_2.setVisibility(View.GONE);
            rg_gift_help.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(RadioGroup radioGroup, int i) {
                    final int selectedId = radioGroup.getCheckedRadioButtonId();
                    RadioButton radioSexButton = (RadioButton) dialog.findViewById(selectedId);
                    temp__ = radioSexButton.getText().toString();
                }
            });
            final TextView tv_a_title = (TextView) dialog.findViewById(R.id.tv_a_title);
            tv_a_title.setText("Delivery Confirmation");
            final TextView tv_gift_help = (TextView) dialog.findViewById(R.id.tv_gift_help);
            tv_gift_help.setText("Save");
            tv_gift_help.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    if (isInternetConnected(myActivity)) {
                        ((MyOrdersActivity) myActivity).confirm_order_received(order_id, temp__.toUpperCase());
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    private void order_help_dialog(final String order_id) {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity);

            // Get the layout inflater
            LayoutInflater inflater = myActivity.getLayoutInflater();
            // Inflate and set the layout for the dialog
            // Pass null as the parent view because its going in the dialog layout
            issueBuilder.setView(inflater.inflate(R.layout.dialog_order_help, null));

            final Dialog dialog = issueBuilder.create();

            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
//			dialog.getWindow().getAttributes().windowAnimations = R.style.DialogAnimation;
//			Window window = dialog.getWindow();
            dialog.show();

            final EditText et_gift_comments = (EditText) dialog.findViewById(R.id.et_gift_comments);
            final RadioGroup rg_gift_help = (RadioGroup) dialog.findViewById(R.id.rg_gift_help);

            TextView text = (TextView) dialog.findViewById(R.id.tv_gift_help);
            text.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {

                    if (isInternetConnected(myActivity)) {
                        int selectedId = rg_gift_help.getCheckedRadioButtonId();
                        RadioButton radioSexButton = (RadioButton) dialog.findViewById(selectedId);
                        continueGiftHelp(radioSexButton.getText().toString(), order_id, et_gift_comments.getText().toString().trim() + "");
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    public void continueGiftHelp(String support_type, String order_id, String comment) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("order_id", order_id);
        params.put("support_type", support_type);
        params.put("comment", comment);

        //the_engineer_id,order_id,support_type,comment
        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_submit_support_with_respect_to_order, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("submit_gift_help", str + "");
                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        myActivity.finish();
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
}

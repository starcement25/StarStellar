package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Intent;
import android.net.Uri;
import android.text.TextUtils;   // 🔹 IMPORTANT
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.loopj.android.http.AsyncHttpClient;
import com.loopj.android.http.AsyncHttpResponseHandler;
import com.loopj.android.http.RequestParams;

import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Objects;

import cz.msebera.android.httpclient.Header;
import forcepower.com.star_stellar.Activity.Engineer.DataSet.OrderDataSet;
import forcepower.com.star_stellar.Activity.Engineer.MyOrdersActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.AllUrl.api_save_feedback;
import static forcepower.com.star_stellar.Class.AllUrl.ws_submit_support_with_respect_to_order;
import static forcepower.com.star_stellar.Class.CommonClass.DEFAULT_TIMEOUT;
import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_id;

public class MyOrderDeliveredAdapter extends BaseAdapter {

    private final Activity myActivity;
    private final ArrayList<OrderDataSet> p_item_list;
    private String temp__ = "YES";
    private ProgressDialog progressDialogObj;

    public MyOrderDeliveredAdapter(Activity myActivity, ArrayList<OrderDataSet> p_item_list_) {
        this.myActivity = myActivity;
        this.p_item_list = p_item_list_;
    }

    @Override
    public int getCount() {
        return p_item_list.size();
    }

    @Override
    public OrderDataSet getItem(int position) {
        return p_item_list.get(position);
    }

    @Override
    public long getItemId(int position) {
        return 0;
    }

    @SuppressLint({"SetTextI18n", "InflateParams"})
    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        ViewHolder holder;
        final LayoutInflater mInflater = (LayoutInflater) myActivity.getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_my_order_delivered, null);
            holder = new ViewHolder();

            holder.textViewProductName = convertView.findViewById(R.id.textViewProductName);
            holder.textViewProductCount = convertView.findViewById(R.id.textViewProductCount);
            holder.textViewOrderId = convertView.findViewById(R.id.textViewOrderId);
            holder.textViewDeliveryDate = convertView.findViewById(R.id.textViewDeliveryDate);
            holder.complaintButton = convertView.findViewById(R.id.complaintButton);
            holder.acknowledgementButton = convertView.findViewById(R.id.acknowledgementButton);
            holder.btnNotDelivered = convertView.findViewById(R.id.btnNotDelivered);
            holder.textViewOrderStatus = convertView.findViewById(R.id.textViewOrderStatus);
            holder.trackOrderButton = convertView.findViewById(R.id.trackOrderButton);
            holder.trackingOrderId = convertView.findViewById(R.id.tvAmazonOrderId);
            holder.llTrackingId = convertView.findViewById(R.id.llTrackingId);
            holder.tvDate = convertView.findViewById(R.id.tvDate);
            holder.tvRemarks = convertView.findViewById(R.id.tvRemarks);
            holder.llRemarks = convertView.findViewById(R.id.llRemarks);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        OrderDataSet item = p_item_list.get(position);

        // Set text fields
        holder.textViewProductName.setText(item.getProductName());
        holder.tvDate.setText(item.getDate());
        if(item.getTvRemarks().length() > 0 && item.getTvRemarks() != "null"){
            Log.d("remarks---",item.getTvRemarks());
            holder.llRemarks.setVisibility(View.VISIBLE);
            holder.tvRemarks.setText(item.getTvRemarks());
        }else{
            holder.llRemarks.setVisibility(View.GONE);

        }

        // 🔹 ROW-WISE POINTS / TDS / TOTAL
        holder.textViewProductCount.setText(buildPointsBlock(item));

        holder.textViewOrderId.setText("Order id : " + item.getOrderId());
        holder.textViewDeliveryDate.setText(
                item.getDateOfDeliver().isEmpty()
                        ? "Delivery Date : To be Updated"
                        : "Delivery Date : " + item.getDateOfDeliver()
        );
        holder.textViewOrderStatus.setText(item.getOrderStatus());

        // Tracking id visibility
        if (item.getAmazonOrderId().isEmpty()) {
            holder.llTrackingId.setVisibility(View.GONE);
            holder.trackingOrderId.setText("-");
        } else {
            holder.llTrackingId.setVisibility(View.VISIBLE);
            holder.trackingOrderId.setText(item.getAmazonOrderId());
        }

        // Track order button (link)
        if (item.getAmazonOrderLink().isEmpty()) {
            holder.trackOrderButton.setVisibility(View.GONE);
        } else {
            holder.trackOrderButton.setVisibility(View.VISIBLE);
            holder.trackOrderButton.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW);
                intent.setData(Uri.parse(item.getAmazonOrderLink()));
                v.getContext().startActivity(intent);
            });
        }

        // ✅ Button visibility logic
        String ackFlag = item.getAcknowledgement_btn();
        String feedbackFlag = item.getFeedbackButton();

        boolean showAck = "YES".equalsIgnoreCase(ackFlag);
        boolean showFeedback = "YES".equalsIgnoreCase(feedbackFlag);

        // Acknowledge Delivery button
        holder.acknowledgementButton.setVisibility(showAck ? View.VISIBLE : View.GONE);

        // Not Delivered button – show using same condition as Acknowledge Delivery
        holder.btnNotDelivered.setVisibility(showAck ? View.VISIBLE : View.GONE);

        // Feedback / Complaint button
        holder.complaintButton.setVisibility(showFeedback ? View.VISIBLE : View.GONE);

        // ✅ Acknowledge Delivery click with alert
        holder.acknowledgementButton.setOnClickListener(v -> {
            if (isInternetConnected(myActivity)) {
                new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme)
                        .setMessage("Are you sure you have recieved this product")
                        .setPositiveButton("Yes", (dialog, which) -> {
                            send_Feedback_API(
                                    item.getOrderId(),
                                    get_E_id(myActivity),
                                    item.getGiftId(),
                                    "ACKNOWLEDGEMENT",
                                    ""
                            );
                        })
                        .setNegativeButton("Cancel", null)
                        .show();
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        });

        // ✅ Not Delivered click with alert
        holder.btnNotDelivered.setOnClickListener(v -> {
            if (isInternetConnected(myActivity)) {
                new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme)
                        .setMessage("Are you sure this product is not delivered to you?")
                        .setPositiveButton("Yes", (dialog, which) -> {
                            send_Feedback_API(
                                    item.getOrderId(),
                                    get_E_id(myActivity),
                                    item.getGiftId(),
                                    "ACKNOWLEDGEMENT",
                                    "Not Delivered"
                            );
                        })
                        .setNegativeButton("Cancel", null)
                        .show();
            } else {
                Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
            }
        });

        // Feedback / Complaint
        holder.complaintButton.setOnClickListener(view -> order_help_dialog(item));

        return convertView;
    }

    public void setFilter(ArrayList<OrderDataSet> approved_list, String type) {
        notifyDataSetChanged();
    }

    public static class ViewHolder {
        TextView textViewProductName, textViewProductCount, textViewOrderId, tvDate,
                textViewDeliveryDate, textViewOrderStatus, trackingOrderId, tvRemarks;
        ImageView imageViewProductImage;
        Button acknowledgementButton, complaintButton, trackOrderButton, btnNotDelivered;
        LinearLayout llTrackingId, llRemarks;
    }

    @SuppressLint("InflateParams")
    private void order_help_dialog(final OrderDataSet item) {
        try {
            final AlertDialog.Builder issueBuilder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
            LayoutInflater inflater = myActivity.getLayoutInflater();
            issueBuilder.setView(inflater.inflate(R.layout.dialog_order_help, null));
            final Dialog dialog = issueBuilder.create();
            dialog.setCanceledOnTouchOutside(true);
            dialog.setCancelable(true);
            dialog.show();

            final EditText et_gift_comments = dialog.findViewById(R.id.et_gift_comments);
            TextView text = dialog.findViewById(R.id.tv_gift_help);

            et_gift_comments.setVisibility(View.VISIBLE);

            text.setOnClickListener(v -> {
                if (isInternetConnected(myActivity)) {
                    if (et_gift_comments.getText().toString().trim().isEmpty()) {
                        Toast.makeText(myActivity, "Please write some feedback ", Toast.LENGTH_SHORT).show();
                    } else {
                        send_Feedback_API(
                                item.getOrderId(),
                                get_E_id(myActivity),
                                item.getGiftId(),
                                "FEEDBACK",
                                et_gift_comments.getText().toString()
                        );
                        dialog.dismiss();
                    }
                } else {
                    Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                }
            });
        } catch (final Exception ignored) {
        }
    }

    public void continueGiftHelp(String support_type, String order_id, String comment) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("the_engineer_id", get_E_id(myActivity));
        params.put("order_id", order_id);
        params.put("support_type", support_type);
        params.put("comment", comment);

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);
        client.post(ws_submit_support_with_respect_to_order, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                final String str = new String(responseBody);
                try {
                    final JSONObject reader = new JSONObject(str);
                    print_Log_d("submit_gift_help", str);
                    Toast.makeText(myActivity, reader.optString("process_message"), Toast.LENGTH_SHORT).show();
                    if (reader.optString("process_status").equalsIgnoreCase("YES")) {
                        myActivity.finish();
                    }
                } catch (final Exception ignored) {
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

    // ---------------------------- API CALL ----------------------------
    public void send_Feedback_API(final String orderId, final String userId, final String giftId,
                                  final String feedbackType, final String feedbackText) {
        loadDialog();

        final RequestParams params = new RequestParams();
        params.put("order_id", orderId);
        params.put("user_id", userId);
        params.put("gift_id", giftId);
        params.put("feedback_type", feedbackType);

        if (feedbackText != null) {
            params.put("feedback", feedbackText);
        }

        Log.d("params_", params.toString());

        final AsyncHttpClient client = new AsyncHttpClient();
        client.setTimeout(DEFAULT_TIMEOUT);

        client.post(api_save_feedback, params, new AsyncHttpResponseHandler() {
            @Override
            public void onSuccess(int statusCode, Header[] headers, byte[] responseBody) {
                dismissDialog();
                final String strResponse = new String(responseBody);
                try {
                    JSONObject reader = new JSONObject(strResponse);
                    String msg = reader.optString("message", "Feedback submitted successfully");
                    Toast.makeText(myActivity, msg, Toast.LENGTH_SHORT).show();

                    if (myActivity instanceof MyOrdersActivity) {
                        MyOrdersActivity.the_max_date_time_A = "";
                        ((MyOrdersActivity) myActivity).page_no_A = 1;
                        ((MyOrdersActivity) myActivity).get_Approve_List(1, "", "fresh_a");
                    }
                } catch (Exception e) {
                    e.printStackTrace();
                    Toast.makeText(myActivity, "Response parse error", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(int statusCode, Header[] headers, byte[] responseBody, Throwable error) {
                dismissDialog();
                Toast.makeText(myActivity, "Failed to submit feedback", Toast.LENGTH_SHORT).show();
            }
        });
    }

    public void updateList(ArrayList<OrderDataSet> newList) {
        this.p_item_list.clear();
        this.p_item_list.addAll(newList);
        notifyDataSetChanged();
    }

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

    // ----------------------------------------------------
    // 🔹 Helper: build 3-line block on right side
    //      Points - product_point_text
    //      TDS    - tds_text
    //      Total  - point_taken_text
    // ----------------------------------------------------
    private String buildPointsBlock(OrderDataSet item) {
        String productPointText = safe(item.getProductPointText()); // value of product_point_text
        String tdsText = safe(item.getTdsText());                   // value of tds_text
        String totalPoint = safe(item.getTokenPoint());             // value of point_taken_text

        StringBuilder sb = new StringBuilder();

        if (!TextUtils.isEmpty(productPointText)) {
            sb.append("Points - ").append(productPointText);
        }

        if (!TextUtils.isEmpty(tdsText)) {
            if (sb.length() > 0) sb.append("\n");
            sb.append("TDS - ").append(tdsText);
        }

        if (!TextUtils.isEmpty(totalPoint)) {
            if (sb.length() > 0) sb.append("\n");
            sb.append("Total - ").append(totalPoint);
        }

        if (sb.length() == 0) {
            return "-";
        }

        return sb.toString();
    }

    private String safe(String value) {
        if (value == null) return "";
        value = value.trim();
        if ("null".equalsIgnoreCase(value)) return "";
        return value;
    }
}

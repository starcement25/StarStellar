package forcepower.com.star_stellar.Activity.TE.Adapter;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.msg_Dialog;

import android.app.Activity;
import android.app.Dialog;
import android.content.DialogInterface;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.TE.TePlaceOrderActivity;
import forcepower.com.star_stellar.Class.PlaceOrderModel;
import forcepower.com.star_stellar.R;

public class TePlaceOrderAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {
    private final int VIEW_TYPE_ITEM = 0;
    private final int VIEW_TYPE_LOADING = 1;

    private Activity mContext;
    private ArrayList<PlaceOrderModel> values = new ArrayList<>();

    public TePlaceOrderAdapter(final ArrayList<PlaceOrderModel> pending_list_, final Activity mContext) {
        this.values = pending_list_;
        this.mContext = mContext;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull final ViewGroup parent, final int viewType) {
        if (viewType == VIEW_TYPE_ITEM) {
            final View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_item_te_place_order, parent, false);
            return new ItemViewHolder(view);
        } else {
            final View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_loading, parent, false);
            return new LoadingViewHolder(view);
        }
    }

    @Override
    public void onBindViewHolder(@NonNull final RecyclerView.ViewHolder viewHolder, final int position) {

        if (viewHolder instanceof ItemViewHolder) {
            populateItemRows((ItemViewHolder) viewHolder, position);
        } else if (viewHolder instanceof LoadingViewHolder) {
            showLoadingView((LoadingViewHolder) viewHolder, position);
        }

    }

    @Override
    public int getItemCount() {
        return values.size();
    }

    public int getItemCount_() {
        return values.size();
    }

    /**
     * The following method decides the type of ViewHolder to display in the RecyclerView
     *
     * @param position
     * @return
     */
    @Override
    public int getItemViewType(final int position) {
        return values.get(position) != null ? VIEW_TYPE_ITEM : VIEW_TYPE_LOADING;
    }

    public void setFilter(final ArrayList<PlaceOrderModel> pending_list_) {
        values.addAll(pending_list_);
    }


    private class ItemViewHolder extends RecyclerView.ViewHolder {
        private final TextView tv_prod_name, tv_qty_bags,
                tv_date_and_time, tv_rssd_name, tv_lifting_date, tv_remarks,
                tv_oq_submit, tv_status_remarks;
        private LinearLayout ll_status_remarks;

        public ItemViewHolder(@NonNull final View convertView) {
            super(convertView);
            tv_prod_name = (TextView) convertView.findViewById(R.id.tv_prod_name);
            tv_qty_bags = (TextView) convertView.findViewById(R.id.tv_qty_bags);
            tv_date_and_time = (TextView) convertView.findViewById(R.id.tv_date_and_time);
            tv_rssd_name = (TextView) convertView.findViewById(R.id.tv_rssd_name);
            tv_lifting_date = (TextView) convertView.findViewById(R.id.tv_lifting_date);
            tv_remarks = (TextView) convertView.findViewById(R.id.tv_remarks);

            tv_oq_submit = (TextView) convertView.findViewById(R.id.tv_oq_submit);
            tv_status_remarks = (TextView) convertView.findViewById(R.id.tv_status_remarks);
            ll_status_remarks = (LinearLayout) convertView.findViewById(R.id.ll_status_remarks);
        }
    }

    private class LoadingViewHolder extends RecyclerView.ViewHolder {
        private ProgressBar progressBar;

        public LoadingViewHolder(@NonNull final View itemView) {
            super(itemView);
            progressBar = itemView.findViewById(R.id.progressBar);
        }
    }

    private void showLoadingView(final LoadingViewHolder viewHolder, final int position) {
        //ProgressBar would be displayed
    }

    private void populateItemRows(final ItemViewHolder viewHolder, final int position) {
        viewHolder.tv_prod_name.setText(values.get(position).getProd_name());
        viewHolder.tv_qty_bags.setText(values.get(position).getQty_bags());
        viewHolder.tv_date_and_time.setText(values.get(position).getDate_and_time());
        viewHolder.tv_rssd_name.setText(values.get(position).getRssd_name());
        viewHolder.tv_lifting_date.setText(values.get(position).getDate_of_lifting());
        viewHolder.tv_remarks.setText(values.get(position).getRemarks());
        viewHolder.tv_status_remarks.setText(values.get(position).getStatus_remarks());


        if (values.get(position).getStatus_from_app().isEmpty()) {
            viewHolder.tv_oq_submit.setText("Submit");
            viewHolder.ll_status_remarks.setVisibility(View.GONE);
        } else {
            if (values.get(position).getStatus_from_app().equalsIgnoreCase("Reject")) {
                viewHolder.ll_status_remarks.setVisibility(View.VISIBLE);
            } else {
                viewHolder.ll_status_remarks.setVisibility(View.GONE);
            }
            viewHolder.tv_oq_submit.setText(values.get(position).getStatus_from_app());
        }

        if (viewHolder.tv_oq_submit.getText().toString().equalsIgnoreCase("Submit")) {
            viewHolder.tv_oq_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    update_status_dialog(values.get(position).getOrder_query_id());
                }
            });
        } else {
            viewHolder.tv_oq_submit.setOnClickListener(null);
        }
    }

    private String status_from_app = "", status_remarks = "";

    public void update_status_dialog(final String order_query_id) {
        try {
            final Dialog dialog = new Dialog(mContext, R.style.MyDialog);
            dialog.setContentView(R.layout.dialog_oq_submit);

            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
            status_from_app = "";
            status_remarks = "";
            final ImageView ivBack = (ImageView) dialog.findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    dialog.dismiss();
                }
            });
            final RadioGroup rg_status = (RadioGroup) dialog.findViewById(R.id.rg_status);
            rg_status.setOnCheckedChangeListener(new RadioGroup.OnCheckedChangeListener() {
                @Override
                public void onCheckedChanged(RadioGroup group, int checkedId) {
                    final RadioButton radioButton = group.findViewById(checkedId);
                    status_from_app = radioButton.getText().toString();
                }
            });

            final EditText et_reject_note = (EditText) dialog.findViewById(R.id.et_reject_note);
            final TextView tv_status_r_submit = (TextView) dialog.findViewById(R.id.tv_status_r_submit);
            tv_status_r_submit.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    status_remarks = et_reject_note.getText().toString().trim();

                    if (status_from_app.trim().isEmpty()) {
                        msg_Dialog(mContext, "Please select a option", false);
                    } else if (status_from_app.equalsIgnoreCase("Reject") &&
                            status_remarks.isEmpty()) {
                        msg_Dialog(mContext, "Enter reason for rejection", false);
                    } else {
                        if (isInternetConnected(mContext)) {
                            dialog.dismiss();
                            ((TePlaceOrderActivity) mContext).new Downloading(mContext, order_query_id, status_from_app, status_remarks).execute();
                        } else {
                            msg_Dialog(mContext, checkInternetConnection, false);
                        }
                    }
                }
            });

        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
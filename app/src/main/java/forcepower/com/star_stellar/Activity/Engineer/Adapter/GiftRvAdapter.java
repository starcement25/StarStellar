package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.DialogInterface;
import android.content.Intent;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.EngGiftsActivity;
import forcepower.com.star_stellar.Activity.Engineer.GiftConfirmActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_E_points;

public class GiftRvAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {
    public static final int VIEW_TYPE_ITEM = 0;
    public static final int VIEW_TYPE_LOADING = 1;

    Activity myActivity;
    private ArrayList<CommonHelper> values = new ArrayList<>();
    String email, phone;

    public GiftRvAdapter(ArrayList<CommonHelper> pending_list_, Activity myActivity, String email, String phone) {
        values = pending_list_;
        this.myActivity = myActivity;
        this.email = email;
        this.phone = phone;
    }

    public void updateContactInfo(String email, String phone) {
        this.email = email;
        this.phone = phone;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        if (viewType == VIEW_TYPE_ITEM) {
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_item_gifts, parent, false);
            return new ItemViewHolder(view);
        } else {
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_loading, parent, false);
            return new LoadingViewHolder(view);
        }
    }

    @Override
    public void onBindViewHolder(@NonNull RecyclerView.ViewHolder viewHolder, int position) {
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

    @Override
    public int getItemViewType(int position) {
        return values.get(position) != null ? VIEW_TYPE_ITEM : VIEW_TYPE_LOADING;
    }

    public void setFilter(ArrayList<CommonHelper> pending_list_) {
        values.addAll(pending_list_);
    }

    private class ItemViewHolder extends RecyclerView.ViewHolder {

        ImageView iv_gift_item, iv_gift_info;
        TextView tv_gift_item_name, tv_gift_item_price;

        // TDS value views
//        TextView tv_tds_price_value;
//        TextView tv_tds_percentage;
//        TextView tv_tds_points;

        public ItemViewHolder(@NonNull View itemView) {
            super(itemView);
            iv_gift_item = itemView.findViewById(R.id.iv_gift_item);
            iv_gift_info = itemView.findViewById(R.id.iv_gift_info);
            tv_gift_item_name = itemView.findViewById(R.id.tv_gift_item_name);
            tv_gift_item_price = itemView.findViewById(R.id.tv_gift_item_price);

//            tv_tds_price_value = itemView.findViewById(R.id.tv_tds_price_value);
//            tv_tds_percentage  = itemView.findViewById(R.id.tv_tds_percentage);
//            tv_tds_points      = itemView.findViewById(R.id.tv_tds_points);
        }
    }

    private class LoadingViewHolder extends RecyclerView.ViewHolder {

        ProgressBar progressBar;

        public LoadingViewHolder(@NonNull View itemView) {
            super(itemView);
            progressBar = itemView.findViewById(R.id.progressBar);
        }
    }

    private void showLoadingView(LoadingViewHolder viewHolder, int position) {
        //ProgressBar would be displayed
    }

    private void populateItemRows(ItemViewHolder viewHolder, final int position) {
        viewHolder.iv_gift_info.setOnClickListener(view ->
                giftInfo(values.get(position).getItem2()) //gift_description
        );

        // gift_image_url
        Glide.with(myActivity).load(values.get(position).getItem3())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .dontAnimate()
                .error(R.drawable.default_)
                .into(viewHolder.iv_gift_item);

        viewHolder.tv_gift_item_name.setText(values.get(position).getItem1()); //gift_title
        viewHolder.tv_gift_item_price.setText(values.get(position).getItem5()); //point_require_text

        // demo TDS values (replace with real fields if you have them)
//        viewHolder.tv_tds_price_value.setText("25");
//        viewHolder.tv_tds_percentage.setText("10%");
//        viewHolder.tv_tds_points.setText("2.5");

        if (values.get(position).getItem6().equalsIgnoreCase("ENABLE")) {
            //iewHolder.tv_gift_item_price.setBackgroundResource(R.drawable.border_grey);
            viewHolder.tv_gift_item_price.setClickable(true);
            viewHolder.tv_gift_item_price.setTextColor(myActivity.getResources().getColor(R.color.colorBlack));

            viewHolder.tv_gift_item_price.setOnClickListener(view -> {
                Log.d("total_points--",values.get(position).getItem8());
                Log.d("total_points---",get_E_points(myActivity));
                Log.d("total_points---",values.get(position).getItem1());
                Log.d("total_points---",values.get(position).getItem2());
                Log.d("total_points---",values.get(position).getItem3());
                Log.d("total_points---",values.get(position).getItem4());
                Log.d("total_points---",values.get(position).getItem5());
                Log.d("total_points---",values.get(position).getItem6());
                Log.d("total_points---",values.get(position).getItem7());
                Log.d("total_points---",values.get(position).getItem8());
                Log.d("total_points---",values.get(position).getItem9());
                if( Double.parseDouble(values.get(position).getItem8()) > Double.parseDouble(get_E_points(myActivity))){

                    TextView customTitle = new TextView(myActivity);
                    customTitle.setText("Insufficient Balance");
                    customTitle.setTextSize(18);
                    customTitle.setTypeface(null, android.graphics.Typeface.BOLD);
                    customTitle.setTextColor(myActivity.getResources().getColor(R.color.colorPrimary));
                    customTitle.setPadding(40, 40, 40, 20);
                    customTitle.setGravity(android.view.Gravity.CENTER);
                    AlertDialog dialog = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme)
                            .setCustomTitle(customTitle)
                            .setMessage("Your account does not have sufficient balance to place the order including TDS")
                            .setPositiveButton("Back", (d, which) -> d.dismiss())
                            .setCancelable(true)
                            .create();

                    dialog.show();
                }else{
                    if (isInternetConnected(myActivity)) {
                        Log.d("GIFT_CLICK", "Redeem clicked: " + values.get(position).getItem1());
                        Log.d("GIFT_CLICK", "Redeem clicked: " + values.toString());

                        Intent intent = new Intent(myActivity, GiftConfirmActivity.class);
                        intent.putExtra("gift_id", values.get(position).getItem0()); //gift_id
                        intent.putExtra("gift_title", values.get(position).getItem1()); //gift_title
                        intent.putExtra("gift_description", values.get(position).getItem2()); //gift_description
                        intent.putExtra("gift_image_url", values.get(position).getItem3()); //gift_image_url
                        intent.putExtra("point_require", values.get(position).getItem4()); //point_require
                        intent.putExtra("point_require_text", values.get(position).getItem5()); //point_require_text
                        intent.putExtra("button_status", values.get(position).getItem6()); //button_status
                        intent.putExtra("e_points", get_E_points(myActivity)); //e_points
                        intent.putExtra("is_email_required", values.get(position).getboolValue());//is_email_required
                        intent.putExtra("email", email);
                        intent.putExtra("phone", phone);

                        if (myActivity instanceof EngGiftsActivity) {
                            ((EngGiftsActivity) myActivity).showTermsPopup(intent);
                        } else {
                            myActivity.startActivity(intent); // fallback
                        }
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }

                }
            });
        } else {
            viewHolder.tv_gift_item_price.setClickable(true);
//            viewHolder.tv_gift_item_price.setOnClickListener(v -> {
//
//            });
            viewHolder.tv_gift_item_price.setTextColor(myActivity.getResources().getColor(R.color.custom_text_inactive));
            viewHolder.tv_gift_item_price.setBackgroundResource(R.drawable.border_disable_solid);
        }
    }

    private void giftInfo(String msg) {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity,R.style.WhiteDialogTheme);
            builder.setMessage(msg + "");

            builder.setPositiveButton("OK", (dialog, which) -> {});
            AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

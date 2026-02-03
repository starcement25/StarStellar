package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.DialogInterface;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.EngGiftsActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.SharedPrefData.getDeviceWidth;

public class HomeTopPicsAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {
    private final int VIEW_TYPE_ITEM = 0;
    private final int VIEW_TYPE_LOADING = 1;

    Activity myActivity;
    private ArrayList<CommonHelper> values = new ArrayList<>();

    public HomeTopPicsAdapter(ArrayList<CommonHelper> pending_list_, Activity myActivity) {
        values = pending_list_;
        this.myActivity = myActivity;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        if (viewType == VIEW_TYPE_ITEM) {
            View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_item_gifts_home, parent, false);
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

    /**
     * The following method decides the type of ViewHolder to display in the RecyclerView
     *
     * @param position
     * @return
     */
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
        LinearLayout ll_home_gift;

        public ItemViewHolder(@NonNull View itemView) {
            super(itemView);
            iv_gift_item = itemView.findViewById(R.id.iv_gift_item);
            iv_gift_info = itemView.findViewById(R.id.iv_gift_info);
            tv_gift_item_name = itemView.findViewById(R.id.tv_gift_item_name);
            tv_gift_item_price = itemView.findViewById(R.id.tv_gift_item_price);
            ll_home_gift = itemView.findViewById(R.id.ll_home_gift);
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
        viewHolder.ll_home_gift.getLayoutParams().width = (getDeviceWidth(myActivity) / 3) - 30;
        viewHolder.iv_gift_info.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                giftInfo(values.get(position).getItem2()); //gift_description
            }
        });
        //gift_image_url
        Glide.with(myActivity).load(values.get(position).getItem3())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .skipMemoryCache(true)
                .dontAnimate()
                .error(R.drawable.default_)
                .into(viewHolder.iv_gift_item);


        viewHolder.tv_gift_item_name.setText(values.get(position).getItem1()); //gift_title
        viewHolder.iv_gift_item.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (isInternetConnected(myActivity)) {
                    Intent intent = new Intent(myActivity, EngGiftsActivity.class);
                    myActivity.startActivity(intent);
                } else {
                    Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                }
            }
        });

    }

    private void giftInfo(String msg) {
        try {
            AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");

            builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {

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
}

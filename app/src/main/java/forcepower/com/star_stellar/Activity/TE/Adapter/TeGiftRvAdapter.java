package forcepower.com.star_stellar.Activity.TE.Adapter;

import android.app.Activity;
import android.content.DialogInterface;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.RecyclerView;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

public class TeGiftRvAdapter extends RecyclerView.Adapter<RecyclerView.ViewHolder> {
    private final int VIEW_TYPE_ITEM = 0;
    private final int VIEW_TYPE_LOADING = 1;

    private Activity myActivity;
    private ArrayList<CommonHelper> values = new ArrayList<>();

    public TeGiftRvAdapter(final ArrayList<CommonHelper> pending_list_, final Activity myActivity) {
        this.values = pending_list_;
        this.myActivity = myActivity;
    }

    @NonNull
    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(@NonNull final ViewGroup parent, final int viewType) {
        if (viewType == VIEW_TYPE_ITEM) {
            final View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.list_item_gifts, parent, false);
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

    public void setFilter(final ArrayList<CommonHelper> pending_list_) {
        values.addAll(pending_list_);
    }


    private class ItemViewHolder extends RecyclerView.ViewHolder {
        private final ImageView iv_gift_item, iv_gift_info;
        private final TextView tv_gift_item_name, tv_gift_item_price;

        public ItemViewHolder(@NonNull final View itemView) {
            super(itemView);
            iv_gift_item = itemView.findViewById(R.id.iv_gift_item);
            iv_gift_info = itemView.findViewById(R.id.iv_gift_info);
            tv_gift_item_name = itemView.findViewById(R.id.tv_gift_item_name);
            tv_gift_item_price = itemView.findViewById(R.id.tv_gift_item_price);
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
        viewHolder.iv_gift_info.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                giftInfo(values.get(position).getItem2()); //gift_description
            }
        });
        //gift_image_url
        Glide.with(myActivity).load(values.get(position).getItem3())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .dontAnimate()
                .error(R.drawable.default_)
                .into(viewHolder.iv_gift_item);


        viewHolder.tv_gift_item_name.setText(values.get(position).getItem1()); //gift_title
        viewHolder.tv_gift_item_price.setText(values.get(position).getItem5()); //point_require_text

        if (values.get(position).getItem6().equalsIgnoreCase("ENABLE")) {
            viewHolder.tv_gift_item_price.setBackgroundResource(R.drawable.border_grey);
            viewHolder.tv_gift_item_price.setClickable(true);
            viewHolder.tv_gift_item_price.setTextColor(myActivity.getResources().getColor(R.color.colorBlack));
            viewHolder.tv_gift_item_price.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    /*if(isInternetConnected(myActivity))
                    {
                        Intent intent = new Intent(myActivity, GiftConfirmActivity.class);
                        intent.putExtra("gift_id", values.get(position).getItem0()); //gift_id
                        intent.putExtra("gift_title", values.get(position).getItem1()); //gift_title
                        intent.putExtra("gift_description", values.get(position).getItem2()); //gift_description
                        intent.putExtra("gift_image_url", values.get(position).getItem3()); //gift_image_url
                        intent.putExtra("point_require", values.get(position).getItem4()); //point_require
                        intent.putExtra("point_require_text", values.get(position).getItem5()); //point_require_text
                        intent.putExtra("button_status", values.get(position).getItem6()); //button_status
                        intent.putExtra("e_points", values.get(position).getItem7()); //e_points
                        myActivity.startActivity(intent);
                        myActivity.finish();
                    }
                    else
                    {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }*/
                }
            });
        } else {
            viewHolder.tv_gift_item_price.setClickable(false);
            viewHolder.tv_gift_item_price.setTextColor(myActivity.getResources().getColor(R.color.custom_text_inactive));
            viewHolder.tv_gift_item_price.setBackgroundResource(R.drawable.border_disable_solid);
        }
    }

    private void giftInfo(final String msg) {
        try {
            final AlertDialog.Builder builder = new AlertDialog.Builder(myActivity);
            builder.setMessage(msg + "");

            builder.setPositiveButton("OK", new DialogInterface.OnClickListener() {
                @Override
                public void onClick(DialogInterface dialog, int which) {

                }
            });
            final AlertDialog dialog = builder.create();
            dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
            dialog.setCancelable(true);
            dialog.setCanceledOnTouchOutside(true);
            dialog.show();
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }
}

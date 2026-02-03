package forcepower.com.star_stellar.Activity.TE.Adapter;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_id;

import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.List;

import forcepower.com.star_stellar.Activity.TE.TeUpdateLiftingActivity;
import forcepower.com.star_stellar.R;
import forcepower.com.star_stellar.notifications.UpdateLiftingModel;

public class UpdateLiftingAdapter_New extends RecyclerView.Adapter {
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;

    private List<UpdateLiftingModel> studentList;

    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    private OnLoadMoreListener_MyEng onLoadMoreListener;
    private Activity myActivity;


    public UpdateLiftingAdapter_New(final Activity myActivity_, final List<UpdateLiftingModel> students,
                                    final RecyclerView recyclerView) {

        myActivity = myActivity_;
        studentList = students;

        if (recyclerView.getLayoutManager() instanceof LinearLayoutManager) {

            final LinearLayoutManager linearLayoutManager = (LinearLayoutManager) recyclerView
                    .getLayoutManager();


            recyclerView
                    .addOnScrollListener(new RecyclerView.OnScrollListener() {
                        @Override
                        public void onScrolled(RecyclerView recyclerView,
                                               int dx, int dy) {
                            super.onScrolled(recyclerView, dx, dy);

                            totalItemCount = linearLayoutManager.getItemCount();
                            lastVisibleItem = linearLayoutManager
                                    .findLastVisibleItemPosition();
                            if (!loading
                                    && totalItemCount <= (lastVisibleItem + visibleThreshold)) {
                                // End has been reached
                                // Do something
                                if (onLoadMoreListener != null) {
                                    onLoadMoreListener.onLoadMore();
                                }
                                loading = true;
                            }
                        }
                    });
        }
    }

    @Override
    public int getItemViewType(int position) {
        return studentList.get(position) != null ? VIEW_ITEM : VIEW_PROG;
    }

    @Override
    public RecyclerView.ViewHolder onCreateViewHolder(ViewGroup parent,
                                                      int viewType) {
        RecyclerView.ViewHolder vh;
        if (viewType == VIEW_ITEM) {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.list_item_eng_lifting, parent, false);

            vh = new UpdateLiftingModelViewHolder(v);
        } else {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.item_loading, parent, false);

            vh = new ProgressViewHolder(v);
        }
        return vh;
    }

    @Override
    public void onBindViewHolder(RecyclerView.ViewHolder holder, int position) {
        if (holder instanceof UpdateLiftingModelViewHolder) {

            final UpdateLiftingModel singleUpdateLiftingModel = (UpdateLiftingModel) studentList.get(position);
            ((UpdateLiftingModelViewHolder) holder).student = singleUpdateLiftingModel;

            //e_profile_image_url
            Glide.with(myActivity).load(singleUpdateLiftingModel.getE_profile_image_url())
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .dontAnimate()
                    .error(R.drawable.en_profile)
                    .into(((UpdateLiftingModelViewHolder) holder).iv_my_engg_dp);


            ((UpdateLiftingModelViewHolder) holder).tv_lifting_engg_name.setText(singleUpdateLiftingModel.getE_name()); //e_name
            ((UpdateLiftingModelViewHolder) holder).tv_lifting_mobile.setText(singleUpdateLiftingModel.getE_mobile()); //
            ((UpdateLiftingModelViewHolder) holder).tv_lifting_address.setText(singleUpdateLiftingModel.getE_city_town()); //


        } else {
            ((ProgressViewHolder) holder).progressBar.setIndeterminate(true);
        }
    }

    public void setLoaded() {
        loading = false;
    }

    @Override
    public int getItemCount() {
        return studentList.size();
    }

    public void setOnLoadMoreListener(OnLoadMoreListener_MyEng onLoadMoreListener) {
        this.onLoadMoreListener = onLoadMoreListener;
    }


    //
    public class UpdateLiftingModelViewHolder extends RecyclerView.ViewHolder {
        final ImageView iv_my_engg_dp;
        final TextView tv_lifting_engg_name, tv_lifting_mobile, tv_lifting_address;
        private UpdateLiftingModel student;

        public UpdateLiftingModelViewHolder(View itemView) {
            super(itemView);

            iv_my_engg_dp = itemView.findViewById(R.id.iv_my_engg_dp);
            tv_lifting_engg_name = itemView.findViewById(R.id.tv_lifting_engg_name);
            tv_lifting_mobile = itemView.findViewById(R.id.tv_lifting_mobile);
            tv_lifting_address = itemView.findViewById(R.id.tv_lifting_address);

            itemView.setOnClickListener(new OnClickListener() {

                @Override
                public void onClick(View v) {
                    print_Log_d("UPDATE_LIFTING ", " ");
                    if (isInternetConnected(myActivity)) {
                        set_E_id(myActivity, student.getEid()); //eid
                        ((TeUpdateLiftingActivity) myActivity).getExistingSiteList();
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }

                }
            });
        }
    }

    public static class ProgressViewHolder extends RecyclerView.ViewHolder {
        final ProgressBar progressBar;

        public ProgressViewHolder(View v) {
            super(v);
            progressBar = (ProgressBar) v.findViewById(R.id.progressBar);
        }
    }
}
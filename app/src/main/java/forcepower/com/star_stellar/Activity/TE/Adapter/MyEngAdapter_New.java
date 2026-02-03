package forcepower.com.star_stellar.Activity.TE.Adapter;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
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

import forcepower.com.star_stellar.Activity.TE.TeProfileViewActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;
import static forcepower.com.star_stellar.Class.SharedPrefData.set_E_id;

public class MyEngAdapter_New extends RecyclerView.Adapter {
    private final int VIEW_ITEM = 1;
    private final int VIEW_PROG = 0;

    private List<Student_6> studentList;

    // The minimum amount of items to have below your current scroll position
    // before loading more.
    private int visibleThreshold = 5;
    private int lastVisibleItem, totalItemCount;
    private boolean loading;
    private OnLoadMoreListener_MyEng onLoadMoreListener;
    private Activity myActivity;


    public MyEngAdapter_New(final Activity myActivity_, final List<Student_6> students,
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
                    R.layout.list_item_my_eng, parent, false);

            vh = new Student_6ViewHolder(v);
        } else {
            final View v = LayoutInflater.from(parent.getContext()).inflate(
                    R.layout.item_loading, parent, false);

            vh = new ProgressViewHolder(v);
        }
        return vh;
    }

    @Override
    public void onBindViewHolder(RecyclerView.ViewHolder holder, int position) {
        if (holder instanceof Student_6ViewHolder) {

            final Student_6 singleStudent_6 = (Student_6) studentList.get(position);
            ((Student_6ViewHolder) holder).student = singleStudent_6;

            //e_profile_image_url
            Glide.with(myActivity).load(singleStudent_6.gete_profile_image_url())
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .dontAnimate()
                    .error(R.drawable.en_profile)
                    .into(((Student_6ViewHolder) holder).iv_my_engg_dp);


            if (singleStudent_6.gete_status().equalsIgnoreCase("Active")) {
                ((Student_6ViewHolder) holder).tv_status_circle.setBackgroundResource(R.drawable.border_ring_green);
            } else if (singleStudent_6.gete_status().equalsIgnoreCase("Inactive")) {
                ((Student_6ViewHolder) holder).tv_status_circle.setBackgroundResource(R.drawable.border_ring_white);
            } else //if(values.get(position).getItem0().equalsIgnoreCase("Semiactive"))
            {
                ((Student_6ViewHolder) holder).tv_status_circle.setBackgroundResource(R.drawable.border_ring_orange);
            }

            ((Student_6ViewHolder) holder).tv_my_engg_name.setText(singleStudent_6.gete_name()); //e_name
            ((Student_6ViewHolder) holder).tv_my_engg_address.setText(singleStudent_6.gete_city_town()); //e_city_town
            ((Student_6ViewHolder) holder).tv_my_engg_recomm_date.setText(singleStudent_6.get_r_submission_date()); //r_submission_date
            ((Student_6ViewHolder) holder).tv_my_engg_mobile.setText(singleStudent_6.get_e_mobile()); //r_submission_date

//			((Student_6ViewHolder) holder).tv_my_engg_mobile.setOnClickListener(new OnClickListener() {
//				@Override
//				public void onClick(View view) {
//					if(isValidMobile(singleStudent_6.get_e_mobile()))
//					{
//						Intent intent = new Intent(Intent.ACTION_DIAL);
//						intent.setData(Uri.parse("tel:"+singleStudent_6.get_e_mobile()));
//						myActivity.startActivity(intent);
//					}
//				}
//			});

            ((Student_6ViewHolder) holder).itemView.setOnClickListener(new OnClickListener() {

                @Override
                public void onClick(View v) {
                    print_Log_d("recommended_site_P", " ");
                    if (isInternetConnected(myActivity)) {
                        Intent intent = new Intent(myActivity, TeProfileViewActivity.class);
                        intent.putExtra("eid", singleStudent_6.get_ok_Eid()); //eid
                        set_E_id(myActivity, singleStudent_6.get_ok_Eid()); //eid
                        myActivity.startActivity(intent);
                    } else {
                        Toast.makeText(myActivity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                    }

                }
            });
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
    public class Student_6ViewHolder extends RecyclerView.ViewHolder {
        final ImageView iv_my_engg_dp;
        final TextView tv_my_engg_name, tv_my_engg_address, tv_my_engg_recomm_date, tv_status_circle,
                tv_my_engg_mobile;
        final LinearLayout ll_my_Engineer;
        private Student_6 student;

        public Student_6ViewHolder(View itemView) {
            super(itemView);

            iv_my_engg_dp = itemView.findViewById(R.id.iv_my_engg_dp);
            tv_my_engg_name = itemView.findViewById(R.id.tv_my_engg_name);
            tv_my_engg_address = itemView.findViewById(R.id.tv_my_engg_address);
            tv_my_engg_recomm_date = itemView.findViewById(R.id.tv_my_engg_recomm_date);
            ll_my_Engineer = itemView.findViewById(R.id.ll_my_Engineer);
            tv_my_engg_mobile = itemView.findViewById(R.id.tv_my_engg_mobile);
            tv_status_circle = itemView.findViewById(R.id.tv_status_circle);

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
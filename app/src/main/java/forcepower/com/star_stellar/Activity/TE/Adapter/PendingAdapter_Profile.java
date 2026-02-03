package forcepower.com.star_stellar.Activity.TE.Adapter;

import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

import forcepower.com.star_stellar.Activity.TE.TeProfileRecommendationDetails_P;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.print_Log_d;

public class PendingAdapter_Profile extends RecyclerView.Adapter {
	private final int VIEW_ITEM = 1;
	private final int VIEW_PROG = 0;

	private List<Student> studentList;

	// The minimum amount of items to have below your current scroll position
	// before loading more.
	private int visibleThreshold = 5;
	private int lastVisibleItem, totalItemCount;
	private boolean loading;
	private OnLoadMoreListener_P onLoadMoreListener;
	public Activity myActivity;


	public PendingAdapter_Profile(Activity myActivity_, List<Student> students, RecyclerView recyclerView) {
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
			View v = LayoutInflater.from(parent.getContext()).inflate(
					R.layout.list_item_site_a, parent, false);

			vh = new StudentViewHolder(v);
		} else {
			View v = LayoutInflater.from(parent.getContext()).inflate(
					R.layout.item_loading, parent, false);

			vh = new ProgressViewHolder(v);
		}
		return vh;
	}

	@Override
	public void onBindViewHolder(RecyclerView.ViewHolder holder, int position)
	{
		if (holder instanceof StudentViewHolder)
		{
			
			Student singleStudent= (Student) studentList.get(position);

			((StudentViewHolder) holder).tv_point_earned.setText(singleStudent.get_r_recomended_by()); //tv_point_earned
			((StudentViewHolder) holder).tv_P_item.setText(singleStudent.getName());//site_name

			((StudentViewHolder) holder).tv_P_details.setText(singleStudent.getEmailId()); //recommended_date
			((StudentViewHolder) holder).tv_status_circle.setBackgroundResource(R.drawable.border_ring_red_ii);
			((StudentViewHolder) holder).student= singleStudent;
			
		}
		else
		{
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

	public void setOnLoadMoreListener(OnLoadMoreListener_P onLoadMoreListener) {
		this.onLoadMoreListener = onLoadMoreListener;
	}


	//
	public class StudentViewHolder extends RecyclerView.ViewHolder {
		TextView tv_P_item, tv_P_details, tv_point_earned, tv_status_circle;
		LinearLayout ll_recommended;
		Student student;
		public StudentViewHolder(View itemView) {
			super(itemView);
			tv_P_item = itemView.findViewById(R.id.tv_P_item);
			tv_P_details = itemView.findViewById(R.id.tv_P_details);
			tv_status_circle = itemView.findViewById(R.id.tv_status_circle);
			ll_recommended = itemView.findViewById(R.id.ll_recommended);
			tv_point_earned = itemView.findViewById(R.id.tv_point_earned);

			itemView.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					print_Log_d("recommended_site_P"," ");
					Intent intent = new Intent(myActivity, TeProfileRecommendationDetails_P.class);
					intent.putExtra("recommended_site_details", student.get_json_row());//json_row
					myActivity.startActivity(intent);
//					myActivity.finish();

				}
			});
		}
	}

	public static class ProgressViewHolder extends RecyclerView.ViewHolder {
		public ProgressBar progressBar;

		public ProgressViewHolder(View v) {
			super(v);
			progressBar = (ProgressBar) v.findViewById(R.id.progressBar);
		}
	}
}
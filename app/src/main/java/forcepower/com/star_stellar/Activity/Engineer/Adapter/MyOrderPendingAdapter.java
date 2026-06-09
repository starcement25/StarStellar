package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.Intent;
import android.net.Uri;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;


public class MyOrderPendingAdapter extends BaseAdapter {

    private Activity myActivity;
    private ArrayList<CommonHelper> p_item_list = new ArrayList<>();

    public MyOrderPendingAdapter(Activity myActivity, ArrayList<CommonHelper> p_item_list_) {
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
    public void updateList(ArrayList<CommonHelper> newList) {
        this.p_item_list.clear();
        this.p_item_list.addAll(newList);
        notifyDataSetChanged();
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        ViewHolder holder = null;
        final LayoutInflater mInflater = (LayoutInflater) myActivity
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_my_order, null);
            holder = new ViewHolder();

            holder.tv_P_item = (TextView) convertView.findViewById(R.id.tv_P_item);
            holder.tv_P_details = (TextView) convertView.findViewById(R.id.tv_P_details);
            //holder.iv_gift_item = (ImageView) convertView.findViewById(R.id.iv_gift_item);
//			holder.tv_gift_Status = (TextView) convertView.findViewById(R.id.tv_gift_Status);
            holder.tv_P_order_id = (TextView) convertView.findViewById(R.id.tv_P_order_id);
            holder.tv_order_del_date = (TextView) convertView.findViewById(R.id.tv_order_del_date);
            holder.tv_gift_delivered = (TextView) convertView.findViewById(R.id.tv_gift_delivered);
            holder.tvAmazonOrderId = (TextView) convertView.findViewById(R.id.tvAmazonOrderId);
            holder.trackingBtn = (TextView) convertView.findViewById(R.id.btnTrackOrder);

            holder.tv_json_row = (TextView) convertView.findViewById(R.id.tv_json_row);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

//		holder.tv_gift_Status.setText("");
        holder.tv_gift_delivered.setVisibility(View.GONE);
//		holder.tv_gift_Status.setTextColor(myActivity.getResources().getColor(R.color.red));
//        Glide.with(myActivity).load(p_item_list.get(position).getItem1())
//                .diskCacheStrategy(DiskCacheStrategy.NONE)
//                .dontAnimate()
//                .into(holder.iv_gift_item);
        holder.tv_P_item.setText(p_item_list.get(position).getItem0());
        holder.tv_P_details.setText(p_item_list.get(position).getItem4()); //point_taken_text
        holder.tv_P_order_id.setText("Order id : " + p_item_list.get(position).getItem5()); //order_id
        if (!p_item_list.get(position).getItem6().matches("")) {
            holder.tv_order_del_date.setText("Expected Delivery Date : " + p_item_list.get(position).getItem6()); //expected_delivery_date

        } else {
            holder.tv_order_del_date.setText("Expected Delivery Date : To be Updated "); //expected_delivery_date
        }
        if (!p_item_list.get(position).getItem8().isEmpty()){
            holder.trackingBtn.setVisibility(View.VISIBLE);
            holder.tvAmazonOrderId.setText("Tracking Order Id - " + p_item_list.get(position).getItem7());
            holder.trackingBtn.setOnClickListener(v -> {
                Intent intent = new Intent(Intent.ACTION_VIEW);
                intent.setData(Uri.parse(p_item_list.get(position).getItem8()));
                v.getContext().startActivity(intent);
            });
        }
        holder.tv_json_row.setText(p_item_list.get(position).getItem3()); //


        return convertView;
    }

    public void setFilter(ArrayList<CommonHelper> pending_list, String type) {
        if (type.matches("fresh_p")) {
            this.p_item_list.clear();
        }
        this.p_item_list.addAll(pending_list);
    }

    public class ViewHolder {
        private TextView tv_P_item, tv_P_details, tv_P_order_id, tv_order_del_date,
                tv_gift_delivered, tv_json_row,trackingBtn,tvAmazonOrderId;
    }
}

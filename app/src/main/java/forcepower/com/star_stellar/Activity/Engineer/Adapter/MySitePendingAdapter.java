package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import java.util.ArrayList;

import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;


public class MySitePendingAdapter extends BaseAdapter {
    private Activity context;
    private ArrayList<CommonHelper> p_item_list = new ArrayList<>();

    public MySitePendingAdapter(Activity context, ArrayList<CommonHelper> p_item_list_) {
        this.context = context;
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
    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder holder = null;
        final LayoutInflater mInflater = (LayoutInflater) context
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_site_p, null);
            holder = new ViewHolder();

            holder.tv_P_item = (TextView) convertView.findViewById(R.id.tv_P_item);
            holder.tv_P_details = (TextView) convertView.findViewById(R.id.tv_P_details);
            holder.tv_status_circle = (TextView) convertView.findViewById(R.id.tv_status_circle);
            holder.tv_status_circle = (TextView) convertView.findViewById(R.id.tv_status_circle);
            holder.tv_P_mobile = (TextView) convertView.findViewById(R.id.tv_P_mobile);
            holder.tv_json_row = (TextView) convertView.findViewById(R.id.tv_json_row);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_P_item.setText(p_item_list.get(position).getItem0()); //r_site_name
        holder.tv_P_details.setText(p_item_list.get(position).getItem1()); //r_submission_date
        holder.tv_P_mobile.setText(p_item_list.get(position).getItem5()); //r_mobile_no
        holder.tv_json_row.setText(p_item_list.get(position).getItem3());

        if (p_item_list.get(position).getItem2().equalsIgnoreCase("PENDING")) //r_status)
        {
            holder.tv_status_circle.setBackgroundResource(R.drawable.border_ring_white);
        } else {
            holder.tv_status_circle.setBackgroundResource(R.drawable.border_ring_green);
        }


        return convertView;
    }

    public void setFilter(ArrayList<CommonHelper> pending_list, String type) {
        if (type.matches("fresh_p")) {
            this.p_item_list.clear();
        } else if (type.equalsIgnoreCase("PPP")) {
            p_item_list = new ArrayList<>();
        }
        this.p_item_list.addAll(pending_list);
    }

    public class ViewHolder {
        private TextView tv_P_item, tv_P_details, tv_status_circle, tv_P_mobile, tv_json_row;
    }
}

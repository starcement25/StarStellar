package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.NotificationDetailsActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;


public class NotificationAdapter extends BaseAdapter {
    private Activity myActivity;
    private ArrayList<CommonHelper> ledger_item_list = new ArrayList<>();

    public NotificationAdapter(Activity myActivity, ArrayList<CommonHelper> ledger_item_list_) {
        this.myActivity = myActivity;
        this.ledger_item_list = ledger_item_list_;
    }

    @Override
    public int getCount() {
        return ledger_item_list.size();
    }

    @Override
    public CommonHelper getItem(int position) {

        return ledger_item_list.get(position);
    }

    @Override
    public long getItemId(int position) {

        return 0;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        ViewHolder holder = null;
        final LayoutInflater mInflater = (LayoutInflater) myActivity
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_notification, null);
            holder = new ViewHolder();

            holder.iv_N_item = (ImageView) convertView.findViewById(R.id.iv_N_item);
            holder.tv_N_item = (TextView) convertView.findViewById(R.id.tv_N_item);
            holder.tv_N_details = (TextView) convertView.findViewById(R.id.tv_N_details);
            holder.tv_N_date = (TextView) convertView.findViewById(R.id.tv_N_date);
            holder.ll_noti = (LinearLayout) convertView.findViewById(R.id.ll_noti);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_N_item.setText(ledger_item_list.get(position).getItem1()); //m_title
        holder.tv_N_details.setText(ledger_item_list.get(position).getItem2()); //m_message
        //m_image_link
        Glide.with(myActivity).load(ledger_item_list.get(position).getItem4())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .dontAnimate()
                .placeholder(R.drawable.default_)
                .error(R.drawable.default_)
                .into(holder.iv_N_item);
        holder.tv_N_date.setText(ledger_item_list.get(position).getItem5()); //n_date_time

        holder.ll_noti.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (ledger_item_list.get(position).getItem3().equalsIgnoreCase("PDF")) //m_file_type
                {
                    Intent intent = new Intent(myActivity, NotificationPdfActivity.class);
                    intent.putExtra("id", ledger_item_list.get(position).getItem0()); //nid
                    intent.putExtra("title", ledger_item_list.get(position).getItem1()); //m_title
                    intent.putExtra("message", ledger_item_list.get(position).getItem2()); //m_message
                    intent.putExtra("imageUrl", ledger_item_list.get(position).getItem4()); //m_image_link
                    myActivity.startActivity(intent);
                } else {
                    Intent intent = new Intent(myActivity, NotificationDetailsActivity.class);
                    intent.putExtra("title", ledger_item_list.get(position).getItem1()); //m_title
                    intent.putExtra("message", ledger_item_list.get(position).getItem2()); //m_message
                    intent.putExtra("image", ledger_item_list.get(position).getItem4()); //m_image_link
                    myActivity.startActivity(intent);
                }

            }
        });


        return convertView;
    }

    public void setFilter(ArrayList<CommonHelper> ledger_item_list, String type) {
        if (type.matches("fresh_p")) {
            this.ledger_item_list.clear();
        }
        this.ledger_item_list.addAll(ledger_item_list);
        notifyDataSetChanged();
    }

    public class ViewHolder {
        TextView tv_N_item, tv_N_details, tv_N_date;
        ImageView iv_N_item;
        LinearLayout ll_noti;
    }
}

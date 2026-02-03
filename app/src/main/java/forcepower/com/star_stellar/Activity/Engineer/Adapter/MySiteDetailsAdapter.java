package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.ComponentName;
import android.content.Intent;
import android.graphics.Paint;
import android.net.Uri;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import java.util.ArrayList;

import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;


public class MySiteDetailsAdapter extends BaseAdapter {

    private Activity context;
    private ArrayList<CommonHelper> p_item_list = new ArrayList<>();

    public MySiteDetailsAdapter(Activity context, ArrayList<CommonHelper> p_item_list_) {
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
    public View getView(final int position, View convertView, ViewGroup parent) {
        ViewHolder holder = null;
        final LayoutInflater mInflater = (LayoutInflater) context
                .getSystemService(Activity.LAYOUT_INFLATER_SERVICE);

        if (convertView == null) {
            convertView = mInflater.inflate(R.layout.list_item_profile, null);
            holder = new ViewHolder();

            holder.tv_P_item = (TextView) convertView.findViewById(R.id.tv_P_item);
            holder.tv_P_details = (TextView) convertView.findViewById(R.id.tv_P_details);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_P_item.setText(p_item_list.get(position).getItem0() + " :");
        if (p_item_list.get(position).getItem0().matches("Mobile") ||
                p_item_list.get(position).getItem0().matches("Contact No")) {
            holder.tv_P_details.setPaintFlags(holder.tv_P_details.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            holder.tv_P_details.setClickable(true);
            holder.tv_P_details.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Intent intent = new Intent(Intent.ACTION_DIAL);
                    intent.setData(Uri.parse("tel:" + p_item_list.get(position).getItem1()));
                    context.startActivity(intent);
                }
            });
        } else if (p_item_list.get(position).getItem0().matches("Site Address")) {
            holder.tv_P_details.setPaintFlags(holder.tv_P_details.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            holder.tv_P_details.setClickable(true);
            holder.tv_P_details.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("http://maps.google.com/maps?f=d&daddr=" + p_item_list.get(position).getItem1()));
                    intent.setComponent(new ComponentName("com.google.android.apps.maps", "com.google.android.maps.MapsActivity"));
                    if (intent.resolveActivity(context.getPackageManager()) != null) {
                        context.startActivity(intent);
                    }
                }
            });
        } else if (p_item_list.get(position).getItem0().matches("Email")) {
            holder.tv_P_details.setPaintFlags(holder.tv_P_details.getPaintFlags() | Paint.UNDERLINE_TEXT_FLAG);
            holder.tv_P_details.setClickable(true);
            holder.tv_P_details.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    Intent intent = new Intent(Intent.ACTION_SEND);
                    intent.setType("text/html");
                    intent.putExtra(Intent.EXTRA_EMAIL, p_item_list.get(position).getItem1());
                    intent.putExtra(Intent.EXTRA_SUBJECT, context.getResources().getString(R.string.app_name));
                    intent.putExtra(Intent.EXTRA_TEXT, "I'm using " + context.getResources().getString(R.string.app_name));

                    context.startActivity(Intent.createChooser(intent, "Send Email"));
                }
            });
        } else {
            holder.tv_P_details.setPaintFlags(0);
            holder.tv_P_details.setClickable(false);
        }
        holder.tv_P_details.setText(p_item_list.get(position).getItem1());


        return convertView;
    }

    public void setFilter(ArrayList<CommonHelper> menu_item_list) {
        this.p_item_list.clear();
        this.p_item_list.addAll(menu_item_list);
        notifyDataSetChanged();
    }

    public class ViewHolder {
        TextView tv_P_item, tv_P_details;
    }
}

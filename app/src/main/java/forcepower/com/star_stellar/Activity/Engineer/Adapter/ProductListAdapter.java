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


public class ProductListAdapter extends BaseAdapter {
    private Activity context;
    private ArrayList<CommonHelper> menu_item_list = new ArrayList<>();

    public ProductListAdapter(Activity context, ArrayList<CommonHelper> menu_item_list_) {
        this.context = context;
        this.menu_item_list = menu_item_list_;
    }

    @Override
    public int getCount() {
        return menu_item_list.size();
    }

    @Override
    public CommonHelper getItem(int position) {

        return menu_item_list.get(position);
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
            convertView = mInflater.inflate(R.layout.list_item_side_menu, null);
            holder = new ViewHolder();

            holder.tv_hidden_value = (TextView) convertView.findViewById(R.id.tv_hidden_value);
            holder.tv_Menu_item = (TextView) convertView.findViewById(R.id.tv_Menu_item);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_hidden_value.setText(menu_item_list.get(position).getItem0());
        holder.tv_Menu_item.setText(menu_item_list.get(position).getItem1());


        return convertView;
    }

    public class ViewHolder {
        TextView tv_hidden_value, tv_Menu_item;
    }
}

package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.annotation.SuppressLint;
import android.app.Activity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.ExistingSiteModel;
import forcepower.com.star_stellar.R;


public class ExistingSiteListAdapter extends BaseAdapter {
    private Activity context;
    private ArrayList<ExistingSiteModel> menu_item_list = new ArrayList<>();

    public ExistingSiteListAdapter(Activity context, ArrayList<ExistingSiteModel> menu_item_list_) {
        this.context = context;
        this.menu_item_list = menu_item_list_;
    }

    @Override
    public int getCount() {
        return menu_item_list.size();
    }

    @Override
    public ExistingSiteModel getItem(int position) {

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
            convertView = mInflater.inflate(R.layout.list_item_eng_lifting, null);
            holder = new ViewHolder();

            holder.iv_my_engg_dp = convertView.findViewById(R.id.iv_my_engg_dp);
            holder.tv_lifting_engg_name = convertView.findViewById(R.id.tv_lifting_engg_name);
            holder.tv_lifting_mobile = convertView.findViewById(R.id.tv_lifting_mobile);
            holder.tv_lifting_address = convertView.findViewById(R.id.tv_lifting_address);


            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_lifting_engg_name.setText(menu_item_list.get(position).get_r_site_name());
        holder.tv_lifting_mobile.setText(menu_item_list.get(position).get_r_mobile_no());
        holder.tv_lifting_address.setText(menu_item_list.get(position).get_r_address());


        //e_profile_image_url
        Glide.with(context).load(menu_item_list.get(position).get_r_recomended_site_image_url())
                .diskCacheStrategy(DiskCacheStrategy.NONE)
                .dontAnimate()
                .placeholder(R.drawable.default_)
                .error(R.drawable.default_)
                .into(holder.iv_my_engg_dp);

        return convertView;
    }

    public class ViewHolder {
        private ImageView iv_my_engg_dp;
        private TextView tv_lifting_engg_name, tv_lifting_mobile, tv_lifting_address;
    }

    @SuppressLint("NotifyDataSetChanged")
    public void setFilter(final ArrayList<ExistingSiteModel> val) {
        this.menu_item_list = new ArrayList<>();
        this.menu_item_list.addAll(val);
        notifyDataSetChanged();
    }

}

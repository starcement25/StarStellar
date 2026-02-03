package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import static forcepower.com.star_stellar.Class.CommonClass.checkInternetConnection;
import static forcepower.com.star_stellar.Class.CommonClass.isInternetConnected;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_user_type;

import android.app.Activity;
import android.content.Context;

import androidx.viewpager.widget.PagerAdapter;

import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.bumptech.glide.Glide;
import com.bumptech.glide.load.engine.DiskCacheStrategy;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.EngGiftsActivity;
import forcepower.com.star_stellar.Activity.TE.TeGiftsActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

public class SliderPagerAdapter extends PagerAdapter {
    private Activity activity;
    private ArrayList<CommonHelper> image_arraylist;

    public SliderPagerAdapter(Activity activity, ArrayList<CommonHelper> image_arraylist) {
        this.activity = activity;
        this.image_arraylist = image_arraylist;
    }

    @Override
    public Object instantiateItem(ViewGroup container, int position) {
        final LayoutInflater layoutInflater = (LayoutInflater) activity.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        final View view = layoutInflater.inflate(R.layout.layout_home_slider, container, false);
        try {
            final ImageView im_slider = (ImageView) view.findViewById(R.id.iv_redeem_p);

            Glide.with(activity)
                    .load(image_arraylist.get(position).getItem2()) //slider_image_link
                    .diskCacheStrategy(DiskCacheStrategy.NONE)
                    .skipMemoryCache(true)
                    .placeholder(R.drawable.default_) // optional
                    .error(R.drawable.default_) // optional
                    .into(im_slider);


            final TextView tv_top_section_header_text = (TextView) view.findViewById(R.id.tv_top_section_header_text);
            tv_top_section_header_text.setText(image_arraylist.get(position).getItem1()); //slider_header_text

            final TextView top_section_description_text = (TextView) view.findViewById(R.id.tv_redeem_d);
            top_section_description_text.setText(image_arraylist.get(position).getItem0()); //slider_description_text


            view.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View view) {
                    //slider_category
                    if (image_arraylist.get(position).getItem3().equalsIgnoreCase("GIFT")) {
                        if (isInternetConnected(activity)) {
                            if (get_user_type(activity).equalsIgnoreCase("TE")) {
                                activity.startActivity(new Intent(activity, TeGiftsActivity.class));
                            } else {
                                activity.startActivity(new Intent(activity, EngGiftsActivity.class));
                            }
                        } else {
                            Toast.makeText(activity, checkInternetConnection, Toast.LENGTH_SHORT).show();
                        }
                    }
                }
            });
            //
            container.addView(view);
        } catch (final Exception e) {
            e.printStackTrace();
        }
        return view;
    }

    @Override
    public int getCount() {
        return image_arraylist.size();
    }


    @Override
    public boolean isViewFromObject(View view, Object obj) {
        return view == obj;
    }


    @Override
    public void destroyItem(ViewGroup container, int position, Object object) {
        View view = (View) object;
        container.removeView(view);
    }

    public void setFilter(ArrayList<CommonHelper> slider_image_list) {
        image_arraylist.clear();
        image_arraylist.addAll(slider_image_list);
        notifyDataSetChanged();
    }
}

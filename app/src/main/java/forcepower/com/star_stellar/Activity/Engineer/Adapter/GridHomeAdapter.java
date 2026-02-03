package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import java.util.ArrayList;

import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;

public class GridHomeAdapter extends ArrayAdapter<CommonHelper> {

    private final Activity context;
    private final ArrayList<CommonHelper> values;

    public GridHomeAdapter(Activity context, ArrayList<CommonHelper> values) {
        super(context, 0, values);
        this.context = context;
        this.values = values;
    }


    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        ViewHolder viewHolder;
        if (convertView == null) {
            final LayoutInflater inflater = (LayoutInflater) context
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            convertView = inflater.inflate(R.layout.list_item_grid, parent, false);

            viewHolder = new ViewHolder();
            viewHolder.gridImage = (ImageView) convertView.findViewById(R.id.menu_child_img);
            viewHolder.tv_featureName_T = (TextView) convertView.findViewById(R.id.tv_featureName_T);
            viewHolder.tv_featureName_B = (TextView) convertView.findViewById(R.id.tv_featureName_B);
            convertView.setTag(viewHolder);
        } else {
            viewHolder = (ViewHolder) convertView.getTag();
        }

        viewHolder.gridImage.setImageResource(values.get(position).getintValue0());
        viewHolder.tv_featureName_T.setText(values.get(position).getItem1());
        viewHolder.tv_featureName_B.setText(values.get(position).getItem2());

        return convertView;
    }

    public class ViewHolder {
        private ImageView gridImage;
        private TextView tv_featureName_T, tv_featureName_B;
    }
}

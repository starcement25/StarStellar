package forcepower.com.star_stellar.Activity.Engineer.Adapter;

import android.app.Activity;
import android.content.Intent;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.LinearLayout;
import android.widget.TextView;

import java.util.ArrayList;

import forcepower.com.star_stellar.Activity.Engineer.MySiteDetailsActivity;
import forcepower.com.star_stellar.Class.CommonHelper;
import forcepower.com.star_stellar.R;


public class EngLedgerAdapter extends BaseAdapter {

    private Activity myActivity;
    private ArrayList<CommonHelper> ledger_item_list = new ArrayList<>();

    public EngLedgerAdapter(Activity myActivity, ArrayList<CommonHelper> ledger_item_list_) {
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
            convertView = mInflater.inflate(R.layout.list_item_ledger, null);
            holder = new ViewHolder();

            holder.tv_Legger_Date = (TextView) convertView.findViewById(R.id.tv_Legger_Date);
            holder.tv_Legger_Description = (TextView) convertView.findViewById(R.id.tv_Legger_Description);
            holder.tv_Legger_Earn = (TextView) convertView.findViewById(R.id.tv_Legger_Earn);
            holder.tv_Legger_Redeem = (TextView) convertView.findViewById(R.id.tv_Legger_Redeem);
            holder.ll_mysite = (LinearLayout) convertView.findViewById(R.id.ll_mysite);

            convertView.setTag(holder);
        } else {
            holder = (ViewHolder) convertView.getTag();
        }

        holder.tv_Legger_Date.setText(ledger_item_list.get(position).getItem1());
        holder.tv_Legger_Description.setText(ledger_item_list.get(position).getItem2());
        holder.tv_Legger_Earn.setText(ledger_item_list.get(position).getItem3());
        holder.tv_Legger_Redeem.setText(ledger_item_list.get(position).getItem4());
        holder.ll_mysite.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                if (ledger_item_list.get(position).getItem5().equalsIgnoreCase("SITE_RECOMENDATION")) {
                    Intent intent = new Intent(myActivity, MySiteDetailsActivity.class);
                    intent.putExtra("json_row", ledger_item_list.get(position).getItem6());
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
        TextView tv_Legger_Redeem, tv_Legger_Earn, tv_Legger_Description, tv_Legger_Date;
        LinearLayout ll_mysite;
    }
}

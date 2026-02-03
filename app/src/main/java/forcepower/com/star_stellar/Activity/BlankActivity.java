package forcepower.com.star_stellar.Activity;

import android.app.Activity;
import android.graphics.Color;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.RelativeLayout;
import android.widget.TextView;

import forcepower.com.star_stellar.Class.BaseActivity;
import forcepower.com.star_stellar.R;

import static forcepower.com.star_stellar.Class.CommonClass.defaultColorCode;
import static forcepower.com.star_stellar.Class.CommonClass.statusBarColorCode;
import static forcepower.com.star_stellar.Class.SharedPrefData.get_Header_Height;

public class BlankActivity extends BaseActivity {
    private Activity myActivity;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_blank);

        try {
            myActivity = this;
            //Header_View
            final RelativeLayout rlHeaderView_Home = (RelativeLayout) findViewById(R.id.rlHeaderView);
            rlHeaderView_Home.setBackgroundColor(Color.parseColor(defaultColorCode));

            final LinearLayout llTopView = (LinearLayout) findViewById(R.id.llTopView);
            llTopView.setBackgroundColor(Color.parseColor(statusBarColorCode));
            llTopView.setPadding(0, 0, 0, 0);

            final RelativeLayout rlHeaderView = (RelativeLayout) llTopView.findViewById(R.id.rlHeaderView);
            rlHeaderView.getLayoutParams().height = get_Header_Height(myActivity);

            final TextView tvCaption = (TextView) findViewById(R.id.tvCaption);
            tvCaption.setText("Coming Soon");

            final ImageView ivBack = (ImageView) findViewById(R.id.ivBack);
            ivBack.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onBackPressed();
                }
            });
        } catch (final Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        finish();
    }
}

package forcepower.com.star_stellar.Class;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.constraintlayout.widget.ConstraintLayout;
import androidx.recyclerview.widget.DiffUtil;
import androidx.recyclerview.widget.ListAdapter;
import androidx.recyclerview.widget.RecyclerView;
import forcepower.com.star_stellar.R;


public class GiftCategoryAdapter extends ListAdapter<GiftCategory, GiftCategoryAdapter.CategoryViewHolder> {

    private static final int[] GRADIENTS = {
            R.drawable.bg_category_gradient_terracotta,
            R.drawable.bg_category_gradient_teal,
            R.drawable.bg_category_gradient_olive,
            R.drawable.bg_category_gradient_rose,
            R.drawable.bg_category_gradient_navy,
            R.drawable.bg_category_gradient_amber,
    };

    private static final int[] FALLBACK_ICONS = {
            R.drawable.ic_category_gift,
            R.drawable.ic_category_star,
            R.drawable.ic_category_tag,
            R.drawable.ic_category_box,
            R.drawable.ic_category_heart,
            R.drawable.ic_category_sparkle,
    };

    public interface OnCategoryClickListener {
        void onCategoryClick(GiftCategory category);
    }

    private final OnCategoryClickListener listener;

    public GiftCategoryAdapter(OnCategoryClickListener listener) {
        super(DIFF_CALLBACK);
        this.listener = listener;
    }

    @NonNull
    @Override
    public CategoryViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_gift_category, parent, false);
        return new CategoryViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CategoryViewHolder holder, int position) {
        holder.bind(getItem(position), position);
    }

    // -------------------------------------------------------------------------

    class CategoryViewHolder extends RecyclerView.ViewHolder {

        private final CardView cardCategory;
        private final TextView tvCategoryName;
        private final TextView tvItemCount;
        private final ConstraintLayout viewBackground;

        CategoryViewHolder(@NonNull View itemView) {
            super(itemView);
            cardCategory   = itemView.findViewById(R.id.cardCategory);
            viewBackground = itemView.findViewById(R.id.cl_parent);
            //ivCategoryIcon = itemView.findViewById(R.id.ivCategoryIcon);
            tvCategoryName = itemView.findViewById(R.id.tvCategoryName);
            tvItemCount    = itemView.findViewById(R.id.tvItemCount);
        }

        void bind(GiftCategory category, int position) {
            viewBackground.setBackgroundResource(GRADIENTS[position % GRADIENTS.length]);

            tvCategoryName.setText(category.getName());

            if (category.getItemCount() != null) {
                tvItemCount.setText(category.getItemCount() + " items");
                tvItemCount.setVisibility(View.VISIBLE);
            } else {
                tvItemCount.setVisibility(View.GONE);
            }

            cardCategory.setOnClickListener(v -> {
                if (listener != null) listener.onCategoryClick(category);
            });
        }
    }
    private static final DiffUtil.ItemCallback<GiftCategory> DIFF_CALLBACK =
            new DiffUtil.ItemCallback<GiftCategory>() {

                @Override
                public boolean areItemsTheSame(@NonNull GiftCategory oldItem,
                                               @NonNull GiftCategory newItem) {
                    return oldItem.getId().equals(newItem.getId());
                }

                @Override
                public boolean areContentsTheSame(@NonNull GiftCategory oldItem,
                                                  @NonNull GiftCategory newItem) {
                    // Compare all visible fields
                    return oldItem.getName().equals(newItem.getName())
                            && equals(oldItem.getIconUrl(), newItem.getIconUrl())
                            && equals(oldItem.getItemCount(), newItem.getItemCount());
                }

                private boolean equals(Object a, Object b) {
                    return (a == null && b == null) || (a != null && a.equals(b));
                }
            };
}
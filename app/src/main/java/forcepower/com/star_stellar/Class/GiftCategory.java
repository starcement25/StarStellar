package forcepower.com.star_stellar.Class;

import java.io.Serializable;

public class GiftCategory implements Serializable {

    private String id;

    private String name;

    private String iconUrl;

    private Integer itemCount;


    private String slug;

    public GiftCategory(String id, String name, String iconUrl, Integer itemCount) {
        this.id        = id;
        this.name      = name;
        this.iconUrl   = iconUrl;
        this.itemCount = itemCount;
    }

    // --- Getters ---

    public String getId()        { return id; }
    public String getName()      { return name; }
    public String getIconUrl()   { return iconUrl; }
    public Integer getItemCount(){ return itemCount; }
    public String getSlug()      { return slug; }
}
//
//  MyEngineersCell.swift
//  StarStellar
//
//  Created by Apple on 22/08/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class MyEngineersCell: UITableViewCell {

    
    @IBOutlet weak var imgViewEngineers: FPImageView!
    @IBOutlet weak var lblName: UILabel!
    @IBOutlet weak var lblLocation: UILabel!
    @IBOutlet weak var viewStatus: FPView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }
    
}

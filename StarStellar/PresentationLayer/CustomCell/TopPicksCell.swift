//
//  TopPicksCell.swift
//  StarStellar
//
//  Created by Apple on 16/09/19.
//  Copyright © 2019 Apple. All rights reserved.
//

import UIKit

class TopPicksCell: UICollectionViewCell {

    @IBOutlet weak var imgViewTopPicks: UIImageView!
    @IBOutlet weak var lblTopPicks: UILabel!
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
        
        self.layer.cornerRadius = 2.0
        self.layer.borderWidth = 0.5
        self.layer.borderColor = UIColor.lightGray.cgColor
        
    }

}

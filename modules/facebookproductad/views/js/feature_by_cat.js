/*
*
* Dynamic Ads + Pixel
*
* @author    BusinessTech.fr - https://www.businesstech.fr
* @copyright Business Tech - https://www.businesstech.fr
* @license   Commercial
*
*           ____    _______
*          |  _ \  |__   __|
*          | |_) |    | |
*          |  _ <     | |
*          | |_) |    | |
*          |____/     |_|
*
*/
var FpaFeatureByCat = function (sName) {

	// set name
	this.name = sName;

	// set name
	this.oldVersion = false;

	// set translated js msgs
	this.msgs = {};

	// stock error array
	this.aError = [];

	// set url of admin img
	this.sImgUrl = '';

	// set url of module's web service
	this.sWebService = '';

	// variable to control the generation of the XML content
	this.bGenerateXmlFlag = false;

	//variable to manage autocomplete product for all the module
	this.aParamsAutcomplete = {};

	// set this in obj context
	var oThis = this;

	/**
	 * handleOptionToDisplay() manage the dynamic display for the feature by cat tag
	 *
	 * @param string sTagType
	 */
	this.handleOptionToDisplay = function (sTagType) {
		// initialize the list of elt to show and hide
		var aShow = [];
		var aHide = [];

		switch (sTagType) {
			case 'material':
				oFpa.doSet('#set_tag', 'material');
				aShow = ['#bulk_action_material', '.value_material'];
				aHide = ['#bulk_action_pattern', '#bulk_action_adult','#bulk_action_adult_product', '#bulk_action_gender', '#bulk_action_gender_product', '#bulk_action_tagadult', '#bulk_action_tagadult_product', '.value_pattern', '.value_agegroup', '.value_gender', '.value_tagadult', '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy','.value_energy', '#bulk_action_shipping_label', '.value_shipping_label', '#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure','#bulk_action_base_unit_pricing_measure', '.value_base_unit_pricing_measure'];
				break;
			case 'pattern':
				oFpa.doSet('#set_tag', 'pattern');
				aShow = ['#bulk_action_pattern', '.value_pattern'];
				aHide = ['#bulk_action_material', '#bulk_action_adult','#bulk_action_adult_product', '#bulk_action_gender', '#bulk_action_gender_product', '#bulk_action_tagadult', '#bulk_action_tagadult_product', '.value_material', '.value_agegroup', '.value_gender', '.value_tagadult',  '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy','.value_energy', '#bulk_action_shipping_label', '.value_shipping_label','#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure', '#bulk_action_base_unit_pricing_measure', '.value_base_unit_pricing_measure'];
				break;
			case 'agegroup':
				oFpa.doSet('#set_tag', 'agegroup');
				aShow = ['#bulk_action_adult', '.value_agegroup'];
				aHide = ['#bulk_action_material', '#bulk_action_pattern', '#bulk_action_gender', '#bulk_action_gender_product', '#bulk_action_tagadult', '#bulk_action_tagadult_product', '.value_material', '.value_pattern', '.value_gender', '.value_tagadult' , '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy','.value_energy', '#bulk_action_shipping_label', '.value_shipping_label','#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure', '#bulk_action_base_unit_pricing_measure', '.value_base_unit_pricing_measure'];
				break;
			case 'gender':
				oFpa.doSet('#set_tag', 'gender');
				aShow = ['#bulk_action_gender', '.value_gender'];
				aHide = ['#bulk_action_material', '#bulk_action_pattern', '#bulk_action_adult','#bulk_action_adult_product', '#bulk_action_tagadult', '#bulk_action_tagadult_product', '.value_material', '.value_pattern', '.value_agegroup', '.value_tagadult', '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy','.value_energy', '#bulk_action_shipping_label', '.value_shipping_label','#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure', '#bulk_action_base_unit_pricing_measure', '.value_base_unit_pricing_measure'];
				break;
			case 'adult':
				oFpa.doSet('#set_tag', 'adult');
				aShow = ['#bulk_action_tagadult', '.value_tagadult'];
				aHide = ['#bulk_action_material', '#bulk_action_pattern', '#bulk_action_adult','#bulk_action_adult_product', '#bulk_action_gender', '#bulk_action_gender_product', '.value_material', '.value_pattern', '.value_agegroup', '.value_gender', '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy','.value_energy', '#bulk_action_shipping_label', '.value_shipping_label','#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure', '#bulk_action_base_unit_pricing_measure', '.value_base_unit_pricing_measure'];
				break;
			case '0':
				aHide = ['#bulk_action_material', '#bulk_action_pattern', '#bulk_action_adult','#bulk_action_adult_product', '#bulk_action_gender', '#bulk_action_gender_product', '#bulk_action_tagadult', '#bulk_action_tagadult_product', '.value_material', '.value_pattern', '.value_agegroup', '.value_gender', '.value_tagadult', , '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeType', '.value_sizeType', '#bulk_action_sizeSystem', '.value_sizeSystem', '#bulk_action_energy' ,'#bulk_action_energy_2', '.value_energy',  '#bulk_action_shipping_label', '.value_shipping_label', '#bulk_action_unit_pricing_measure', '.value_unit_pricing_measure'];
				break;
			default:
				break;
		}

		oFpa.initHide(aHide);
		oFpa.initShow(aShow);
	}
}
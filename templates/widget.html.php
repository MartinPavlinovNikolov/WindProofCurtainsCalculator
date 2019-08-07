<h4 id="you-must-know-text">You MUST know</h4>
<a id="mount-text" href="/installation">&gt; Installation</a>
<a id="mesure-text" href="/maintenance-instructions">&gt; Maintenance</a>
<h2 id="order-text">ORDER</h2>
<div id="setup_order_box">
    <div class="order_box">
        <h4 id="choose-color-text">SELECT COLOR</h4>
        <section class="section_select_color">
            <div v-for="(avalible_color, i) in avalible_colors" class="section_select_color__color" :style="{ backgroundColor: avalible_color.backgraundColor }" :title="avalible_color.title" :class="{ selected_color: avalible_color.isSelected }" @click="colorWasSelected(i)">
                <span><i class="fa fa-check"></i></span>
            </div>
        </section>
        <section class="section-upload-location-image">
            <h4>UPLOAD IMAGE</h4>
            <p>(Upload image of the place where you will use windcurtains)</p>
            <form method="POST" enctype="multipart/form-data">
                <input name="image_of_the_place" id="image_of_the_place" type="file" @change="imageWasSelected" @click="resetMsg" accesept="image/*">
            </form>
            <label id="label_for_image_of_the_place" for="image_of_the_place">Choose File</label>
            <span :class="[ imageIsSelected ? 'image-is-selected' : '', 'image-is-not-selected' ]"><i class="fa fa-check"></i></span>
        </section>
        <section class="section-select-measurment">
            <h4>SELECT MEASURMENT</h4>
            <div class="section-select-measurment__radio-buttons">
                <input id="inch" type="radio" name="measurment" value="in" v-model="measurment" @click="measurmentWasSelected">
                <label for="inch">inches</label>
                <input id="cm" type="radio" name="measurment" value="cm" v-model="measurment" @click="measurmentWasSelected">
                <label for="cm">centimetres</label>
            </div>
        </section>
        <hr>
        <section class="section-information-about-walls">
            <h5>INFORMATION ABOUT WALLS</h5>
        </section>
        <section class="section-walls">
            <div class="wall-tabs">
                <input v-for="(wall_tab, wall_tab_index) in wall_tabs" :checked="current_wall_tab_id == wall_tab.id" :id="'wall-tab' + wall_tab.id" type="radio" name="wall">
                <nav>
                    <ul>
                        <li v-for="(wall_tab, wall_tab_index) in wall_tabs" :class="'wall-tab' + (wall_tab.id)">
                            <label :for="'wall-tab' + wall_tab.id" @click="tab_was_changed(wall_tab.id, wall_tab_index)" :class="{ 'wall-tab-is-active': (current_wall_tab_id == wall_tab.id) }">Wall {{ (wall_tab_index + 1) }}</label>
                        </li>
                        <button class="add-wall" @click="addWallTab"><i class="fa fa-plus-circle"></i>&nbsp; &nbsp; ADD WALL</button>
                    </ul>
                </nav>
                <section>
                    <div v-for="(wall_tab, wall_tab_index) in wall_tabs" :class="'wall-tab' + (wall_tab.id)" class="wall-tab-content">
                        <p>SELECT SHAPE OF WALL</p>
                        <span class="delete-wall" @click="removeWallTab(wall_tab_index)"><span><i class="fa fa-trash"></i></span><span>Delete wall</span></span>
                        <div class="available-shapes">
                            <div v-for="(available_shape, available_shape_index) in wall_tab.available_shapes" class="available-shape">
                                <label :for="'available-shape' + available_shape_index + wall_tab_index"><image :src="'<?php echo plugins_url( 'assets/images/shapes/', __DIR__ ); ?>' + available_shape.svg_name" alt="figure" width=50 height="50" /></label>
                                <input :id="'available-shape' + available_shape_index + wall_tab_index" type="radio" :name="'shape' + wall_tab_index" :checked="wall_tab.current_shape_id == available_shape.id" :id="'wall-tab' + wall_tab.id" @click="shapeWasSelected(wall_tab_index, available_shape_index, available_shape.id)">
                            </div>
                            <div class="available-shape-selected">
                                <img :src="'<?php echo plugins_url( 'assets/images/small/', __DIR__ ); ?>' + (wall_tab.current_svg_name)" alt="figure"/>
                                <img v-if="wall_tab.current_svg_name == '3.svg' || wall_tab.current_svg_name == '4.svg' || wall_tab.current_svg_name == '9.svg' || wall_tab.current_svg_name == '10.svg'" src="<?php echo plugins_url( 'assets/images/small/', __DIR__ ) . 'nivelir.png'; ?>" :class="(wall_tab.current_svg_name == '3.svg' || wall_tab.current_svg_name == '4.svg') ? 'move_up_nivelir' : ''"/>
                            </div>
                            <div v-for="(available_shape, available_shape_index) in wall_tab.available_shapes">
                                <div v-if="available_shape.id == wall_tab.current_shape_id">
                                    <div v-for="(dimension, dimension_index) in available_shape.dimensions">
                                        
                                        <div class="section-dimensions">
                                            <h4>DIMENSION <b>{{dimension.letter}}</b></h4>
                                            <div class="section-dimensions__wrapper">
                                                <label>Length:</label>
                                                <input type="number" placeholder="150" v-model="dimension.value" @click="resetMsg">
                                                <span>{{ measurment }}</span>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="section-door-included">
                                <h5>DOOR INCLUDED</h5>
                                <div class="door-included-wrapper">
                                    <input type="checkbox" v-model="wall_tab.door_included">
                                    <label @click="resetMsg">Yes, include door</label>
                                </div>
                            </div>
                            <div v-if="wall_tab.door_included == true" class="section-door-dimentions">
                                <label>Тhe door is located from</br> the bottom leftmost part,</br> at a distance:
                                <input type="number" name="door-dimentions" placeholder="90" v-model="wall_tab.door_starts_from" @click="resetMsg"><span>{{ measurment }}</span></label>
                            </div>
                        </div>
                        <div class="section-additional-information">
                            <h6>ADDITIONAL INFORMATION</h6>
                            <textarea v-model="wall_tab.section_additional_information" @click="resetMsg"></textarea>
                            <p class="characktersLength">{{ (255 - wall_tab.section_additional_information.length) }} characters left.</p>
                        </div>
                    </div>
                    <div class="section-paging">
                        <div class="section-paging-page" @click="changeToLeftTab">&lt;</div>

                        <div v-for="(wall_tab, wall_tab_index) in wall_tabs" class="section-paging-page" :class="(current_wall_tab_id == wall_tab.id) ? 'selected-page' : ''" @click="tab_was_changed(wall_tab.id, wall_tab_index)">{{ wall_tab_index + 1 }}</div>

                        <div class="section-paging-page" @click="changeToRightTab">&gt;</div>
                    </div>
                </section>
            </div>
        </section>
        <p class="current-price-text">CURRENT PRICE</p>
        <h3 class="current-price-value">{{ getPrice }}£</h3>
        <h6 class="current-price-vat">(VAT 20% included)</h6>

        <a style="text-align: center;" type="button" class="button normal alternative-1 order-now" @click="showModalCredentials">ORDER NOW</a>

        <p class="text-danger text-left" v-html="msg !== 'valid' ? msg : ''"></p>
        <form action="/checkout" method="POST">
            <div class="stripe-button"></div>
        </form>
    </div>
</div>
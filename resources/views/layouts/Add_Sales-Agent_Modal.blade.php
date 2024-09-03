<!-- Add Sales Agent Modal -->
<div class="modal fade" id="addSalesAgentModal" tabindex="-1" aria-labelledby="addSalesAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center"
                style="background: linear-gradient(180deg, rgb(255, 180, 206) 0%, hsla(0, 0%, 100%, 1) 100%);border:none;">
                <h5 class="modal-title" id="addSalesAgentModalLabel">
                    <strong style="color: #91264c">Add Sales Agent</strong>
                </h5>
                <!-- Adding a logo on the right side -->
                <img src="{{ url('/images/02-EduCLaaS-Logo-Raspberry-300x94.png') }}" alt="Company Logo"
                    style="height: 30px;">
            </div>
            <div class="modal-body">
                <form action=" {{ route('owner#save-user') }} " method="POST" id="addSalesAgentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row row-margin-bottom row-border-bottom">
                        <div class="-col-md-6">
                            <div class="form-group">
                                <input type="hidden" class="form-control" 
                                name="fk_buh" value=" {{ $user->id }} " readonly>
                                <input type="hidden" name="role" value="Sales_Agent">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="agentName">Sales Agent Name</label>
                                <input type="text" name="agentName" class="form-control fonts" id="agentName"
                                    placeholder="Enter agent name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="font-educ">Email</label>
                                <input type="email" name="email" class="form-control fonts" id="email"
                                placeholder="Enter Email" pattern="[a-zA-Z0-9._%+-]+@(lithan\.com|educlass\.com)$" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="hubspotId">Hubspot ID</label>
                                <input type="text" name="hubspotId" class="form-control fonts" id="hubspotId"
                                    placeholder="Enter Hubspot ID" required pattern="\d+">
                            </div>
                        </div>
                    </div>
                    <div class="row row-margin-bottom ">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="businessUnit">Business Unit</label>
                                <input type="text" name="businessUnit" class="form-control fonts" id="businessUnit"
                                    placeholder="Enter business unit" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-educ" for="country">Country</label>
                                <select name="country" class="form-control fonts" id="country" required>
                                    <option value="">Select a country</option>
                                    <option value="af">Afghanistan</option>
                                    <option value="al">Albania</option>
                                    <option value="dz">Algeria</option>
                                    <option value="ad">Andorra</option>
                                    <option value="ao">Angola</option>
                                    <option value="ag">Antigua and Barbuda</option>
                                    <option value="ar">Argentina</option>
                                    <option value="am">Armenia</option>
                                    <option value="au">Australia</option>
                                    <option value="at">Austria</option>
                                    <option value="az">Azerbaijan</option>
                                    <option value="bs">Bahamas</option>
                                    <option value="bh">Bahrain</option>
                                    <option value="bd">Bangladesh</option>
                                    <option value="bb">Barbados</option>
                                    <option value="by">Belarus</option>
                                    <option value="be">Belgium</option>
                                    <option value="bz">Belize</option>
                                    <option value="bj">Benin</option>
                                    <option value="bt">Bhutan</option>
                                    <option value="bo">Bolivia</option>
                                    <option value="ba">Bosnia and Herzegovina</option>
                                    <option value="bw">Botswana</option>
                                    <option value="br">Brazil</option>
                                    <option value="bn">Brunei</option>
                                    <option value="bg">Bulgaria</option>
                                    <option value="bf">Burkina Faso</option>
                                    <option value="bi">Burundi</option>
                                    <option value="cv">Cabo Verde</option>
                                    <option value="kh">Cambodia</option>
                                    <option value="cm">Cameroon</option>
                                    <option value="ca">Canada</option>
                                    <option value="cf">Central African Republic</option>
                                    <option value="td">Chad</option>
                                    <option value="cl">Chile</option>
                                    <option value="cn">China</option>
                                    <option value="co">Colombia</option>
                                    <option value="km">Comoros</option>
                                    <option value="cg">Congo (Congo-Brazzaville)</option>
                                    <option value="cd">Congo (DRC)</option>
                                    <option value="cr">Costa Rica</option>
                                    <option value="hr">Croatia</option>
                                    <option value="cu">Cuba</option>
                                    <option value="cy">Cyprus</option>
                                    <option value="cz">Czech Republic</option>
                                    <option value="dk">Denmark</option>
                                    <option value="dj">Djibouti</option>
                                    <option value="dm">Dominica</option>
                                    <option value="do">Dominican Republic</option>
                                    <option value="ec">Ecuador</option>
                                    <option value="eg">Egypt</option>
                                    <option value="sv">El Salvador</option>
                                    <option value="gq">Equatorial Guinea</option>
                                    <option value="er">Eritrea</option>
                                    <option value="ee">Estonia</option>
                                    <option value="sz">Eswatini</option>
                                    <option value="et">Ethiopia</option>
                                    <option value="fj">Fiji</option>
                                    <option value="fi">Finland</option>
                                    <option value="fr">France</option>
                                    <option value="ga">Gabon</option>
                                    <option value="gm">Gambia</option>
                                    <option value="ge">Georgia</option>
                                    <option value="de">Germany</option>
                                    <option value="gh">Ghana</option>
                                    <option value="gr">Greece</option>
                                    <option value="gd">Grenada</option>
                                    <option value="gt">Guatemala</option>
                                    <option value="gn">Guinea</option>
                                    <option value="gw">Guinea-Bissau</option>
                                    <option value="gy">Guyana</option>
                                    <option value="ht">Haiti</option>
                                    <option value="hn">Honduras</option>
                                    <option value="hu">Hungary</option>
                                    <option value="is">Iceland</option>
                                    <option value="in">India</option>
                                    <option value="id">Indonesia</option>
                                    <option value="ir">Iran</option>
                                    <option value="iq">Iraq</option>
                                    <option value="ie">Ireland</option>
                                    <option value="il">Israel</option>
                                    <option value="it">Italy</option>
                                    <option value="jm">Jamaica</option>
                                    <option value="jp">Japan</option>
                                    <option value="jo">Jordan</option>
                                    <option value="kz">Kazakhstan</option>
                                    <option value="ke">Kenya</option>
                                    <option value="ki">Kiribati</option>
                                    <option value="kw">Kuwait</option>
                                    <option value="kg">Kyrgyzstan</option>
                                    <option value="la">Laos</option>
                                    <option value="lv">Latvia</option>
                                    <option value="lb">Lebanon</option>
                                    <option value="ls">Lesotho</option>
                                    <option value="lr">Liberia</option>
                                    <option value="ly">Libya</option>
                                    <option value="li">Liechtenstein</option>
                                    <option value="lt">Lithuania</option>
                                    <option value="lu">Luxembourg</option>
                                    <option value="mg">Madagascar</option>
                                    <option value="mw">Malawi</option>
                                    <option value="my">Malaysia</option>
                                    <option value="mv">Maldives</option>
                                    <option value="ml">Mali</option>
                                    <option value="mt">Malta</option>
                                    <option value="mh">Marshall Islands</option>
                                    <option value="mr">Mauritania</option>
                                    <option value="mu">Mauritius</option>
                                    <option value="mx">Mexico</option>
                                    <option value="fm">Micronesia</option>
                                    <option value="md">Moldova</option>
                                    <option value="mc">Monaco</option>
                                    <option value="mn">Mongolia</option>
                                    <option value="me">Montenegro</option>
                                    <option value="ma">Morocco</option>
                                    <option value="mz">Mozambique</option>
                                    <option value="mm">Myanmar</option>
                                    <option value="na">Namibia</option>
                                    <option value="nr">Nauru</option>
                                    <option value="np">Nepal</option>
                                    <option value="nl">Netherlands</option>
                                    <option value="nz">New Zealand</option>
                                    <option value="ni">Nicaragua</option>
                                    <option value="ne">Niger</option>
                                    <option value="ng">Nigeria</option>
                                    <option value="kp">North Korea</option>
                                    <option value="mk">North Macedonia</option>
                                    <option value="no">Norway</option>
                                    <option value="om">Oman</option>
                                    <option value="pk">Pakistan</option>
                                    <option value="pw">Palau</option>
                                    <option value="pa">Panama</option>
                                    <option value="pg">Papua New Guinea</option>
                                    <option value="py">Paraguay</option>
                                    <option value="pe">Peru</option>
                                    <option value="ph">Philippines</option>
                                    <option value="pl">Poland</option>
                                    <option value="pt">Portugal</option>
                                    <option value="qa">Qatar</option>
                                    <option value="ro">Romania</option>
                                    <option value="ru">Russia</option>
                                    <option value="rw">Rwanda</option>
                                    <option value="kn">Saint Kitts and Nevis</option>
                                    <option value="lc">Saint Lucia</option>
                                    <option value="vc">Saint Vincent and the Grenadines</option>
                                    <option value="ws">Samoa</option>
                                    <option value="sm">San Marino</option>
                                    <option value="st">Sao Tome and Principe</option>
                                    <option value="sa">Saudi Arabia</option>
                                    <option value="sn">Senegal</option>
                                    <option value="rs">Serbia</option>
                                    <option value="sc">Seychelles</option>
                                    <option value="sl">Sierra Leone</option>
                                    <option value="sg">Singapore</option>
                                    <option value="sk">Slovakia</option>
                                    <option value="si">Slovenia</option>
                                    <option value="sb">Solomon Islands</option>
                                    <option value="so">Somalia</option>
                                    <option value="za">South Africa</option>
                                    <option value="kr">South Korea</option>
                                    <option value="ss">South Sudan</option>
                                    <option value="es">Spain</option>
                                    <option value="lk">Sri Lanka</option>
                                    <option value="sd">Sudan</option>
                                    <option value="sr">Suriname</option>
                                    <option value="se">Sweden</option>
                                    <option value="ch">Switzerland</option>
                                    <option value="sy">Syria</option>
                                    <option value="tw">Taiwan</option>
                                    <option value="tj">Tajikistan</option>
                                    <option value="tz">Tanzania</option>
                                    <option value="th">Thailand</option>
                                    <option value="tl">Timor-Leste</option>
                                    <option value="tg">Togo</option>
                                    <option value="to">Tonga</option>
                                    <option value="tt">Trinidad and Tobago</option>
                                    <option value="tn">Tunisia</option>
                                    <option value="tr">Turkey</option>
                                    <option value="tm">Turkmenistan</option>
                                    <option value="tv">Tuvalu</option>
                                    <option value="ug">Uganda</option>
                                    <option value="ua">Ukraine</option>
                                    <option value="ae">United Arab Emirates</option>
                                    <option value="gb">United Kingdom</option>
                                    <option value="us">United States of America</option>
                                    <option value="uy">Uruguay</option>
                                    <option value="uz">Uzbekistan</option>
                                    <option value="vu">Vanuatu</option>
                                    <option value="ve">Venezuela</option>
                                    <option value="vn">Vietnam</option>
                                    <option value="ye">Yemen</option>
                                    <option value="zm">Zambia</option>
                                    <option value="zw">Zimbabwe</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="border: none">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" style="background: #91264c; color: white;">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

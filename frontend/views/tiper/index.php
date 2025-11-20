<div style="margin: 50px auto; width: 65%">
    <form class="row g-3" method="post" id="frm01" enctype="multipart/form-data">
        <input type="hidden"
               name="<?= Yii::$app->request->csrfParam; ?>"
               value="<?= Yii::$app->request->getCsrfToken() ?>"
        >
        <input type="hidden" name="Tiper[user]" value="<?= $user ?? -1 ?>">
        <input type="hidden" name="Tiper[source]" value="<?= $source ?? -1 ?>">
        <input type="hidden" name="Tiper[envagy]">
        <h2>Ponúkam nehnuteľnosť</h2>
        <p style="font-size: 0.9rem; font-style: italic; margin:0 0 20px 0;">
            <sup class="redstar">*</sup> označené políčka sú povinné!</p>
        <div id="err-msg" style="display: none"></div>
        <h5>Kontaktné údaje</h5>
        <div class="row g-3">
            <div class="col">
                <label for="firma" class="form-label">Názov firmy</label>
                <input type="text" class="form-control" id="firma" name="Tiper[firma]">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="meno" class="form-label">Meno <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="meno" name="Tiper[meno]">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="priezvisko" class="form-label">Priezvisko <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="priezvisko" name="Tiper[priezvisko]">
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xs-12">
                <label for="tel" class="form-label">Telefónne číslo <sup class="redstar">*</sup></label>
                <input type="tel" class="form-control" id="tel" name="Tiper[telefon]">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="email" class="form-label">Email <sup class="redstar">*</sup></label>
                <input type="email" class="form-control" id="email" name="Tiper[email]">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="diffaddr">
                    <label class="form-check-label" for="diffaddr">
                        Kontaktná adresa majiteľa je iná
                    </label>
                </div>
            </div>
        </div>

        <section id="owner-addr" style="display: none" class="row g-3">
            <h5>Kontaktné údaje majiteľa</h5>
            <div class="row g-3">
                <div class="col-md-6 col-xs-12">
                    <label for="meno" class="form-label">Meno <sup class="redstar">*</sup></label>
                    <input type="text" class="form-control" id="meno" name="Majitel[meno]">
                </div>
                <div class="col-md-6 col-xs-12">
                    <label for="priezvisko" class="form-label">Priezvisko <sup class="redstar">*</sup></label>
                    <input type="text" class="form-control" id="priezvisko" name="Majitel[priezvisko]">
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-6 col-xs-12">
                    <label for="tel" class="form-label">Telefónne číslo <sup class="redstar">*</sup></label>
                    <input type="tel" class="form-control" id="tel" name="Majitel[telefon]">
                </div>
                <div class="col-md-6 col-xs-12">
                    <label for="email" class="form-label">Email <sup class="redstar">*</sup></label>
                    <input type="email" class="form-control" id="email" name="Majitel[email]">
                </div>
            </div>
        </section>

        <h5 class="mt-5">Informácie o nehnuteľnosti</h5>
        <div class="row g-3">
            <div class="col-md-4 col-xs-12">
                <label for="typponuky" class="form-label">Typ ponuky <sup class="redstar">*</sup></label>
                <select class="form-select" id="typponuky" name="Ponuka[offer_type]">
                    <option value="">Zvoľte</option>
                    <option value="1">Predaj</option>
                    <option value="2">Prenájom</option>
                    <option value="3">Kúpa</option>
                    <option value="4">Podnájom</option>
                </select>
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="druhnehnut" class="form-label">Druh nehnuteľnosti <sup class="redstar">*</sup></label>
                <select class="form-select" id="druhnehnut" name="Ponuka[re_type]">
                    <option value="">Zvoľte</option>
                    <optgroup label="BYT">
                        <option value="3">1 izbový byt</option>
                        <option value="4">2 izbový byt</option>
                        <option value="5">3 izbový byt</option>
                        <option value="6">4 izbový byt</option>
                        <option value="7">5 a viac izbový byt</option>
                        <option value="9">Apartmán</option>
                        <option value="2">Dvojgarsónka</option>
                        <option value="1">Garsónka</option>
                        <option value="10">Iný byt</option>
                        <option value="8">Mezonet</option>
                        <option value="71">Štvorbytovka dolná</option>
                        <option value="72">Štvorbytovka horná</option>
                    </optgroup>
                    <optgroup label="DOM">
                        <option value="23">Apartmánový dom</option>
                        <option value="14">Bungalov</option>
                        <option value="18">Chata a chalupa</option>
                        <option value="22">Nájomný dom</option>
                        <option value="70">Radový dom</option>
                        <option value="12">Rodinná vila</option>
                        <option value="11">Rodinný dom</option>
                        <option value="69">Rodinný dvojdom</option>
                        <option value="15">Vidiecky dom</option>
                        <option value="13">Zrubový dom</option>
                        <option value="19">Zruby a drevenice</option>
                    </optgroup>
                    <optgroup label="INÝ OBJEKT">
                        <option value="24">Administratívny objekt</option>
                        <option value="16">Bývalá poľnohosp. usadlosť</option>
                        <option value="37">Čerpacia stanica PHM</option>
                        <option value="39">Garáž</option>
                        <option value="42">Historický objekt</option>
                        <option value="28">Hotel</option>
                        <option value="48">Iné komerčné priestory</option>
                        <option value="52">Iné prevádzkové priestory</option>
                        <option value="31">Iný komerčný objekt</option>
                        <option value="43">Iný objekt</option>
                        <option value="75">Iný objekt na bývanie</option>
                        <option value="21">Iný objekt na rekreáciu</option>
                        <option value="38">Iný prevádzkový objekt</option>
                        <option value="44">Kancelárie, admin. priestory</option>
                        <option value="29">Kúpeľný objekt</option>
                        <option value="41">Malá elektráreň</option>
                        <option value="73">Motel</option>
                        <option value="45">Obchodné priestory</option>
                        <option value="26">Objekt pre obchod</option>
                        <option value="30">Objekt pre šport</option>
                        <option value="51">Opravárenské priestory</option>
                        <option value="36">Opravárenský objekt</option>
                        <option value="74">Penzión</option>
                        <option value="35">Poľnohosp. objekt</option>
                        <option value="25">Polyfunkčný objekt</option>
                        <option value="34">Prevádzkový areál</option>
                        <option value="27">Reštaurácia</option>
                        <option value="46">Reštauračné priestory</option>
                        <option value="50">Skladové priestory</option>
                        <option value="33">Skladový objekt</option>
                        <option value="40">Spevnené plochy</option>
                        <option value="47">Športové priestory</option>
                        <option value="49">Výrobné priestory</option>
                        <option value="32">Výrobný objekt</option>
                        <option value="20">Záhradná chatka</option>
                    </optgroup>
                    <optgroup label="STAVBA">
                        <option value="95">Administratívny objekt</option>
                        <option value="85">Apartmánový dom</option>
                        <option value="80">Bungalov</option>
                        <option value="92">Bývalá poľnohosp. usadlosť</option>
                        <option value="108">Čerpacia stanica PHM</option>
                        <option value="82">Chata a chalupa</option>
                        <option value="110">Garáž</option>
                        <option value="113">Historický objekt</option>
                        <option value="99">Hotel</option>
                        <option value="119">Iné komerčné priestory</option>
                        <option value="123">Iné prevádzkové priestory</option>
                        <option value="102">Iný komerčný objekt</option>
                        <option value="114">Iný objekt</option>
                        <option value="126">Iný objekt na bývanie</option>
                        <option value="94">Iný objekt na rekreáciu</option>
                        <option value="109">Iný prevádzkový objekt</option>
                        <option value="115">Kancelárie, admin. priestory</option>
                        <option value="100">Kúpeľný objekt</option>
                        <option value="112">Malá elektráreň</option>
                        <option value="124">Motel</option>
                        <option value="84">Nájomný dom</option>
                        <option value="116">Obchodné priestory</option>
                        <option value="97">Objekt pre obchod</option>
                        <option value="101">Objekt pre šport</option>
                        <option value="122">Opravárenské priestory</option>
                        <option value="107">Opravárenský objekt</option>
                        <option value="125">Penzión</option>
                        <option value="106">Poľnohosp. objekt</option>
                        <option value="96">Polyfunkčný objekt</option>
                        <option value="105">Prevádzkový areál</option>
                        <option value="87">Radový dom</option>
                        <option value="98">Reštaurácia</option>
                        <option value="117">Reštauračné priestory</option>
                        <option value="78">Rodinná vila</option>
                        <option value="77">Rodinný dom</option>
                        <option value="86">Rodinný dvojdom</option>
                        <option value="121">Skladové priestory</option>
                        <option value="104">Skladový objekt</option>
                        <option value="111">Spevnené plochy</option>
                        <option value="118">Športové priestory</option>
                        <option value="76">Štvorbytovka</option>
                        <option value="81">Vidiecky dom</option>
                        <option value="120">Výrobné priestory</option>
                        <option value="103">Výrobný objekt</option>
                        <option value="93">Záhradná chatka</option>
                        <option value="79">Zrubový dom</option>
                        <option value="83">Zruby a drevenice</option>
                    </optgroup>
                    <optgroup label="POZEMOK">
                        <option value="65">Chmelnica, vinica</option>
                        <option value="68">Iný poľnohosp. pozemok</option>
                        <option value="60">Iný stavebný pozemok</option>
                        <option value="58">Komerčná zóna</option>
                        <option value="66">Lesy</option>
                        <option value="64">Lúka, pasienok</option>
                        <option value="62">Orná pôda</option>
                        <option value="55">Pozemok pre bytovú výstavbu</option>
                        <option value="56">Pozemok pre obč. vybavenosť</option>
                        <option value="53">Pozemok pre rod. domy</option>
                        <option value="59">Priemyselná zóna</option>
                        <option value="54">Rekreačný pozemok</option>
                        <option value="67">Rybník, vodná plocha</option>
                        <option value="63">Sad</option>
                        <option value="61">Záhrada</option>
                        <option value="57">Zmiešaná zóna</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="stavnehnut" class="form-label">Stav nehnuteľnosti <sup class="redstar">*</sup></label>
                <select class="form-select" id="stavnehnut" name="Ponuka[re_status]">
                    <option value="">Zvoľte</option>
                    <option value="62">Čiastočná rekonštrukcia</option>
                    <option value="66">Developerský projekt</option>
                    <option value="63">Kompletná rekonštrukcia</option>
                    <option value="61">Novostavba</option>
                    <option value="64">Pôvodný stav</option>
                    <option value="65">Vo výstavbe</option>
                </select>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="kraj" class="form-label">Kraj <sup class="redstar">*</sup></label>
                <select class="form-select" id="kraj" name="Ponuka[region]">
                    <option value="">Zvoľte</option>
                    <optgroup label="SLOVENSKO">
                        <option value="1">Banskobystrický kraj</option>
                        <option value="2">Bratislavský kraj</option>
                        <option value="3">Košický kraj</option>
                        <option value="4">Nitriansky kraj</option>
                        <option value="5">Prešovský kraj</option>
                        <option value="6">Trenčiansky kraj</option>
                        <option value="7">Trnavský kraj</option>
                        <option value="8">Žilinský kraj</option>
                    </optgroup>
                    <optgroup label="RAKÚSKO">
                        <option value="9">Niederösterreich</option>
                    </optgroup>
                </select>
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="okres" class="form-label">Okres <sup class="redstar">*</sup></label>
                <select class="form-select" id="okres" name="Ponuka[district]">
                    <option value="">Zvoľte</option>
                    <optgroup label="BANSKOBYSTRICKÝ KRAJ">
                        <option value="2">Banská Bystrica</option>
                        <option value="3">Banská Štiavnica</option>
                        <option value="10">Brezno</option>
                        <option value="13">Detva</option>
                        <option value="28">Krupina</option>
                        <option value="33">Lučenec</option>
                        <option value="46">Poltár</option>
                        <option value="52">Revúca</option>
                        <option value="53">Rimavská Sobota</option>
                        <option value="73">Veľký Krtíš</option>
                        <option value="77">Žarnovica</option>
                        <option value="78">Žiar nad Hronom</option>
                        <option value="76">Zvolen</option>
                    </optgroup>
                    <optgroup label="BRATISLAVSKÝ KRAJ">
                        <option value="5">Bratislava I</option>
                        <option value="6">Bratislava II</option>
                        <option value="7">Bratislava III</option>
                        <option value="8">Bratislava IV</option>
                        <option value="9">Bratislava V</option>
                        <option value="34">Malacky</option>
                        <option value="44">Pezinok</option>
                        <option value="57">Senec</option>
                    </optgroup>
                    <optgroup label="KOŠICKÝ KRAJ">
                        <option value="17">Gelnica</option>
                        <option value="23">Košice I</option>
                        <option value="24">Košice II</option>
                        <option value="25">Košice III</option>
                        <option value="26">Košice IV</option>
                        <option value="27">Košice-okolie</option>
                        <option value="37">Michalovce</option>
                        <option value="54">Rožňava</option>
                        <option value="61">Sobrance</option>
                        <option value="62">Spišská Nová Ves</option>
                        <option value="68">Trebišov</option>
                    </optgroup>
                    <optgroup label="NITRIANSKY KRAJ">
                        <option value="22">Komárno</option>
                        <option value="30">Levice</option>
                        <option value="40">Nitra</option>
                        <option value="42">Nové Zámky</option>
                        <option value="66">Šaľa</option>
                        <option value="67">Topoľčany</option>
                        <option value="75">Zlaté Moravce</option>
                    </optgroup>
                    <optgroup label="PREŠOVSKÝ KRAJ">
                        <option value="4">Bardejov</option>
                        <option value="19">Humenné</option>
                        <option value="21">Kežmarok</option>
                        <option value="31">Levoča</option>
                        <option value="36">Medzilaborce</option>
                        <option value="47">Poprad</option>
                        <option value="49">Prešov</option>
                        <option value="56">Sabinov</option>
                        <option value="60">Snina</option>
                        <option value="63">Stará Ľubovňa</option>
                        <option value="64">Stropkov</option>
                        <option value="65">Svidník</option>
                        <option value="74">Vranov nad Topľou</option>
                    </optgroup>
                    <optgroup label="TRENČIANSKY KRAJ">
                        <option value="1">Bánovce nad Bebravou</option>
                        <option value="20">Ilava</option>
                        <option value="38">Myjava</option>
                        <option value="41">Nové Mesto nad Váhom</option>
                        <option value="43">Partizánske</option>
                        <option value="48">Považská Bystrica</option>
                        <option value="50">Prievidza</option>
                        <option value="51">Púchov</option>
                        <option value="69">Trenčín</option>
                    </optgroup>
                    <optgroup label="TRNAVSKÝ KRAJ">
                        <option value="15">Dunajská Streda</option>
                        <option value="16">Galanta</option>
                        <option value="18">Hlohovec</option>
                        <option value="45">Piešťany</option>
                        <option value="58">Senica</option>
                        <option value="59">Skalica</option>
                        <option value="70">Trnava</option>
                    </optgroup>
                    <optgroup label="ŽILINSKÝ KRAJ">
                        <option value="11">Bytča</option>
                        <option value="12">Čadca</option>
                        <option value="14">Dolný Kubín</option>
                        <option value="29">Kysucké Nové Mesto</option>
                        <option value="32">Liptovský Mikuláš</option>
                        <option value="35">Martin</option>
                        <option value="39">Námestovo</option>
                        <option value="55">Ružomberok</option>
                        <option value="71">Turčianske Teplice</option>
                        <option value="72">Tvrdošín</option>
                        <option value="79">Žilina</option>
                    </optgroup>
                    <optgroup label="NIEDERÖSTERREICH">
                        <option value="80">Bruck an der Leitha</option>
                    </optgroup>
                </select>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-3 col-xs-12">
                <label for="psc" class="form-label">PSČ</label>
                <input type="text" class="form-control" id="psc" name="Ponuka[zip]">
            </div>
            <div class="col-md-9 col-xs-12">
                <label for="mesto" class="form-label">Mesto <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="mesto" name="Ponuka[city]">
            </div>
        </div>

        <div class="row g-3">
            <div class="col">
                <label for="mapka" class="form-label">Link na Mapku</label>
                <input type="text" class="form-control" id="mapka" name="Ponuka[mapka]">
                <p class="text-muted fst-italic" style="font-size: small; margin:0">
                    Ako sa dostať na mapku? Otvorte si nasledujúcu linku
                    <a href="https://zbgis.skgeodesy.sk/mkzbgis/sk/kataster" target="_blank">Kataster - Mapka</a>.
                </p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col">
                <label for="ulica" class="form-label">Ulica <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="ulica" name="Ponuka[street]">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4 col-xs-12">
                <label for="supis" class="form-label">Súp. číslo <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="supis" name="Ponuka[reg_num]">
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="orient" class="form-label">Or. čís./vchod <sup class="redstar">*</sup></label>
                <input type="text" class="form-control" id="orient" name="Ponuka[or_num]">
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="byt" class="form-label">Číslo bytu</label>
                <input type="text" class="form-control" id="byt" name="Ponuka[flat_num]">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="cislolv" class="form-label">Číslo LV</label>
                <input type="number" class="form-control" id="cislolv" name="Ponuka[proplist_num]">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="parc" class="form-label">Číslo parcely</label>
                <input type="text" class="form-control" id="parc" name="Ponuka[parc_num]">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="pozemok" class="form-label">Pozemok (m2)</label>
                <input type="number" class="form-control" id="pozemok" name="Ponuka[land]">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="parc" class="form-label">Výmera nehnuteľnosti (m2)</label>
                <input type="number" class="form-control" id="parc" name="Ponuka[parc_num]">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4 col-xs-12">
                <label for="izba" class="form-label">Počet izieb</label>
                <select class="form-select" id="izba" name="Ponuka[izba]">
                    <option value="">Zvoľte</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5 a viac</option>
                </select>
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="kitchen" class="form-label">Počet kuchýň</label>
                <select class="form-select" id="kitchen" name="Ponuka[kitchen]">
                    <option value="">Zvoľte</option>
                    <option value="0">kuchyňský kútik</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5 a viac</option>
                </select>
            </div>
            <div class="col-md-4 col-xs-12">
                <label for="kupelna" class="form-label">Počet kúpelní</label>
                <select class="form-select" id="kupelna" name="Ponuka[kupelna]">
                    <option value="">Zvoľte</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5 a viac</option>
                </select>
            </div>
        </div>
        <div class="row g-3 mb-5">
            <div class="col">
                <label for="notes" class="form-label">Poznámky</label>
                <textarea class="form-control" id="notes" name="Ponuka[note]" rows="10"></textarea>
            </div>
        </div>
        <h5>Obrázky a video</h5>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="img1" class="form-label">Obrázok č.1</label>
                <input type="file" name="Media[img1]" class="form-control" id="img1">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="img2" class="form-label">Obrázok č.2</label>
                <input type="file" name="Media[img2]" class="form-control" id="img2">
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-xs-12">
                <label for="img3" class="form-label">Obrázok č.3</label>
                <input type="file" name="Media[img3]" class="form-control" id="img3">
            </div>
            <div class="col-md-6 col-xs-12">
                <label for="img4" class="form-label">Obrázok č.4</label>
                <input type="file" name="Media[img4]" class="form-control" id="img4">
            </div>
        </div>
        <div class="row g-3">
            <div class="col">
                <label for="video" class="form-label">Video</label>
                <input type="text" name="Media[video]" class="form-control">
                <p class="fst-italic text-muted" style="font-size: small; margin:0">
                    Akceptujeme link YuTube, Vimeo, atď.
                </p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="want2be" name="Ostatne[want2be]">
                    <label class="form-check-label" for="want2be">
                        Chcem sa stať tipérom
                        <p class="fst-italic text-muted" style="font-size: small">
                            Tiper je osoba, ktorá na realitnom trhu nepôsobí,
                            ale prichádza do kontaktu s potenciálnymi záujemcami o kúpu, predaj alebo prenájom
                            nehnuteľností</p>
                    </label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="news">
                    <label class="form-check-label" for="news" name="Ostatne[newsletter]">
                        Tímto dávam súhlas na odber newsletterov od spoločnosti ALPHA-OMEGA REAL & CONSULTING s.r.o.
                    </label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="news">
                    <label class="form-check-label" for="news" name="Ostatne[gdpr]">
                        Súhlasím so správou, spracovaním a uchovaním mojich osobných údajov
                        spoločnosťou ALPHA-OMEGA REAL & CONSULTING s.r.o.
                    </label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-center mt-5">
            <button class="btn btn-secondary">
                Poslať
            </button>
        </div>
    </form>
</div>
<?php
$css = <<<CSS
.redstar{
    color: red;
    font-size: 0.9rem;
}
CSS;
$this->registerCSS($css);
$js = <<<JS
    $(document).on('change', '#diffaddr', function() {
        $('#owner-addr').toggle();
    });
JS;
$this->registerJS($js);

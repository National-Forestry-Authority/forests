<?php

/**
 * @file
 * Post update functions for the farm_nfa module.
 */

use Drupal\Core\Site\Settings;

/**
 * Implements hook_removed_post_updates().
 */
function farm_nfa_removed_post_updates() {
  return [
    'farm_nfa_post_update_001_add_forest_vegetation_terms' => '10.0.0',
    'farm_nfa_post_update_002_add_forest_purpose_terms' => '10.0.0',
  ];
}

/**
 * Change layout of plan page and add image gallery
 */
function farm_nfa_post_update_10002(&$sandbox) {
  farm_nfa_update_helper(10002);
}

/**
 * Populate CFR global id field
 */
function farm_nfa_post_update_cfr_global_ids(&$sandbox) {

  $cfrs = [
    [
      "cfr_id" => "907a4a13-aa65-4f6d-948b-78aab49e2d24",
      "name" => "Abera",
      "asset_id" => "472",
    ],
    [
      "cfr_id" => "87e8b37b-418e-4de7-9271-dc92f4de8c79",
      "name" => "Abiba",
      "asset_id" => "384",
    ],
    [
      "cfr_id" => "a26d5aee-a8f6-47a1-977f-033efc201040",
      "name" => "Abili",
      "asset_id" => "486",
    ],
    [
      "cfr_id" => "2deb4af3-54ac-4ba0-83d4-1ac3ee3fe44c",
      "name" => "Aboke",
      "asset_id" => "400",
    ],
    [
      "cfr_id" => "ea3f8080-b049-46e8-93b3-63c6c4721fb2",
      "name" => "Abuje",
      "asset_id" => "427",
    ],
    [
      "cfr_id" => "32579895-f09e-41d5-a5df-3081b36aed1a",
      "name" => "Abunga",
      "asset_id" => "425",
    ],
    [
      "cfr_id" => "2f5b8f77-bf29-4f74-9ca2-3304ad6a8c84",
      "name" => "Abuya",
      "asset_id" => "320",
    ],
    [
      "cfr_id" => "fc5fd0a6-2916-4328-8615-d9bf2740e381",
      "name" => "Acet",
      "asset_id" => "399",
    ],
    [
      "cfr_id" => "b3db691f-d86d-41ce-b1f2-25f3816383ab",
      "name" => "Achuna",
      "asset_id" => "435",
    ],
    [
      "cfr_id" => "5bfcdcc7-6cad-4c29-bc7e-d4f60ea12948",
      "name" => "Achwa River",
      "asset_id" => "517",
    ],
    [
      "cfr_id" => "37c938c0-f695-43cf-83c3-224c95b1d290",
      "name" => "Achwali",
      "asset_id" => "290",
    ],
    [
      "cfr_id" => "262d61b8-6a7b-40e4-aa0d-65fc017a3733",
      "name" => "Acwao",
      "asset_id" => "389",
    ],
    [
      "cfr_id" => "1db5f81e-e848-4cce-b7ed-0a0fc66063fa",
      "name" => "Adero",
      "asset_id" => "419",
    ],
    [
      "cfr_id" => "e7433415-9446-4cb3-913a-9c2e8c79f0f6",
      "name" => "Aduku (North)",
      "asset_id" => "432",
    ],
    [
      "cfr_id" => "4aa28bd8-d730-4f70-9d5d-8223911b5d35",
      "name" => "Aduku (South)",
      "asset_id" => "438",
    ],
    [
      "cfr_id" => "d712b80f-1728-4b88-b8fc-11f850a13b3d",
      "name" => "Agoro-Agu",
      "asset_id" => "523",
    ],
    [
      "cfr_id" => "aba62bec-f044-4f90-ae10-f8993c08aa4d",
      "name" => "Ajuka",
      "asset_id" => "306",
    ],
    [
      "cfr_id" => "16bb0bb9-2884-4a10-8c82-e03f5037ce9b",
      "name" => "Ajupane",
      "asset_id" => "476",
    ],
    [
      "cfr_id" => "79d9f4a2-f26a-4a53-9cf9-b88e5f1ff8f6",
      "name" => "Akileng",
      "asset_id" => "405",
    ],
    [
      "cfr_id" => "445f1d5b-fc6e-4939-a33d-1579c4121b4a",
      "name" => "Akur",
      "asset_id" => "482",
    ],
    [
      "cfr_id" => "14b52a1e-4bf7-492e-83ac-bc81bee10507",
      "name" => "Alerek",
      "asset_id" => "467",
    ],
    [
      "cfr_id" => "1dd44c1e-7ba6-4646-ac30-2403cbf65e5a",
      "name" => "Alit",
      "asset_id" => "447",
    ],
    [
      "cfr_id" => "a35bb8fc-e057-418c-a21d-26cd0b2fdbd6",
      "name" => "Alito",
      "asset_id" => "395",
    ],
    [
      "cfr_id" => "3c77669a-fdfa-40b3-b9e5-9adee93117cd",
      "name" => "Along-Kongo",
      "asset_id" => "455",
    ],
    [
      "cfr_id" => "2a4d49a0-d56e-411f-9d35-9363aecc3d18",
      "name" => "Aloro",
      "asset_id" => "407",
    ],
    [
      "cfr_id" => "e17e43a2-6f95-4cec-97e8-e47420c27e4b",
      "name" => "Alui",
      "asset_id" => "396",
    ],
    [
      "cfr_id" => "66456cc8-2426-4ba8-ae28-94adc1374283",
      "name" => "Alungamosimosi",
      "asset_id" => "403",
    ],
    [
      "cfr_id" => "619220ea-b36f-49f9-b001-617e7a2c0bcc",
      "name" => "Aminakulu",
      "asset_id" => "439",
    ],
    [
      "cfr_id" => "1b37d6f5-948b-4379-9ee3-69375ca2b54a",
      "name" => "Aminkee",
      "asset_id" => "426",
    ],
    [
      "cfr_id" => "d335ad0b-242f-4b26-a7a6-1c87639f98ad",
      "name" => "Aminteng",
      "asset_id" => "441",
    ],
    [
      "cfr_id" => "5e4eb262-40ac-4235-8596-813fe248d3e4",
      "name" => "Amuka",
      "asset_id" => "465",
    ],
    [
      "cfr_id" => "5aef9001-71ed-4246-8501-6beb39c13e9f",
      "name" => "Aneneng",
      "asset_id" => "397",
    ],
    [
      "cfr_id" => "64621d60-6cf1-44af-9f0b-2900190b769a",
      "name" => "Angutewere",
      "asset_id" => "446",
    ],
    [
      "cfr_id" => "75cb384b-fd01-4ae7-88af-1c809a4f9f54",
      "name" => "Anyara",
      "asset_id" => "436",
    ],
    [
      "cfr_id" => "96fef6e9-77c2-4e2d-b15f-1f4dba26e7ff",
      "name" => "Apac",
      "asset_id" => "437",
    ],
    [
      "cfr_id" => "42b1e899-d1ed-451c-8153-46d893411adb",
      "name" => "Apworocero",
      "asset_id" => "416",
    ],
    [
      "cfr_id" => "7413fd2d-e7b0-4e7d-ad2b-550d68050e98",
      "name" => "Aram",
      "asset_id" => "516",
    ],
    [
      "cfr_id" => "01978d58-ab41-4e64-9f53-2f47684c8ab7",
      "name" => "Aringa River",
      "asset_id" => "502",
    ],
    [
      "cfr_id" => "952296d0-7d42-4f7b-8fe4-a83b54ded545",
      "name" => "Arua",
      "asset_id" => "536",
    ],
    [
      "cfr_id" => "928a3319-52c0-475a-b2c6-81f07709a2d7",
      "name" => "Arweny",
      "asset_id" => "450",
    ],
    [
      "cfr_id" => "22eacf54-14b4-47fe-a32b-c0eab63b2b78",
      "name" => "Atigo",
      "asset_id" => "445",
    ],
    [
      "cfr_id" => "1d87d89f-1652-4476-a21c-f61b68cbec0a",
      "name" => "Ating",
      "asset_id" => "479",
    ],
    [
      "cfr_id" => "ad90409c-838a-4226-ae9b-699bf15a6347",
      "name" => "Atiya",
      "asset_id" => "503",
    ],
    [
      "cfr_id" => "03378ac9-c7bf-47e1-ad54-d7197540eec6",
      "name" => "Atungulo",
      "asset_id" => "297",
    ],
    [
      "cfr_id" => "ee3055b2-b1ed-4206-800c-b361d9e33ca3",
      "name" => "Ave",
      "asset_id" => "471",
    ],
    [
      "cfr_id" => "53f199f0-f58a-4a1f-ae60-60938176e784",
      "name" => "Awang",
      "asset_id" => "492",
    ],
    [
      "cfr_id" => "105cfd55-088c-48ab-90fa-093adfda4d7c",
      "name" => "Awer",
      "asset_id" => "442",
    ],
    [
      "cfr_id" => "dd51b3af-81f2-446b-b6f3-c5f3799d4634",
      "name" => "Ayami",
      "asset_id" => "385",
    ],
    [
      "cfr_id" => "8158e734-8aa1-4703-a2fd-ea7c64603d63",
      "name" => "Ayer (1959 eucalyptus)",
      "asset_id" => "406",
    ],
    [
      "cfr_id" => "0b941c1f-662f-4a3c-b238-b1ccf6073001",
      "name" => "Ayer (Bala Road)",
      "asset_id" => "410",
    ],
    [
      "cfr_id" => "394d4fac-3ef0-4306-8fd5-00aeccef8699",
      "name" => "Ayer (Lira Road)",
      "asset_id" => "404",
    ],
    [
      "cfr_id" => "9ff03400-8b5a-440b-94c0-805d84bab732",
      "name" => "Ayipe",
      "asset_id" => "500",
    ],
    [
      "cfr_id" => "ed6b8d2a-9b48-4c62-a7e3-178b6183e90e",
      "name" => "Ayito",
      "asset_id" => "393",
    ],
    [
      "cfr_id" => "e94bc042-85c1-49df-a48d-a6e033bc2c33",
      "name" => "Bajo",
      "asset_id" => "330",
    ],
    [
      "cfr_id" => "dc5e67b2-564b-443e-adee-43a8d73bee7e",
      "name" => "Bala (North)",
      "asset_id" => "420",
    ],
    [
      "cfr_id" => "9e3bf046-bbc3-4885-aa49-32e2d2b99242",
      "name" => "Bala (South)",
      "asset_id" => "421",
    ],
    [
      "cfr_id" => "452c7999-8b82-4c86-ae40-59dcc0d62433",
      "name" => "Banda Nursery",
      "asset_id" => "161",
    ],
    [
      "cfr_id" => "0d364234-b04b-4882-9477-73085245afb8",
      "name" => "Banga",
      "asset_id" => "78",
    ],
    [
      "cfr_id" => "aea347a9-276f-4edb-b5d7-9bb9a5c76fd3",
      "name" => "Barituku",
      "asset_id" => "525",
    ],
    [
      "cfr_id" => "d237d655-bdd6-4ab6-b1cc-1c432cc6f979",
      "name" => "Bbira",
      "asset_id" => "185",
    ],
    [
      "cfr_id" => "6d62afc7-d468-45e5-a278-ef6dbbf38fc6",
      "name" => "Bikira",
      "asset_id" => "89",
    ],
    [
      "cfr_id" => "9e835981-9376-419e-a203-43a4ffc4c77b",
      "name" => "Bobi",
      "asset_id" => "380",
    ],
    [
      "cfr_id" => "cc85fd3d-8376-4b74-bc9c-19b3a02a0a5f",
      "name" => "Budongo",
      "asset_id" => "428",
    ],
    [
      "cfr_id" => "604e0d63-9002-4b1a-a152-0ebc1e5ecb53",
      "name" => "Budunda",
      "asset_id" => "225",
    ],
    [
      "cfr_id" => "607bad38-5f87-4e2c-947c-b18df01569aa",
      "name" => "Bufumira",
      "asset_id" => "56",
    ],
    [
      "cfr_id" => "766c54c6-ebca-4176-8330-d254fb3e7654",
      "name" => "Buga",
      "asset_id" => "72",
    ],
    [
      "cfr_id" => "bf3fcbc3-4916-4e28-a22c-195f9412d9ca",
      "name" => "Bugaali",
      "asset_id" => "230",
    ],
    [
      "cfr_id" => "c10667e7-74e3-41f2-ad57-02d7be4a07fc",
      "name" => "Bugamba",
      "asset_id" => "104",
    ],
    [
      "cfr_id" => "262d5a76-da04-4ec7-8cdd-7084cc9f57ce",
      "name" => "Bugana",
      "asset_id" => "58",
    ],
    [
      "cfr_id" => "f4078a2e-eaf7-4179-9c38-5030a40d94b3",
      "name" => "Bugiri",
      "asset_id" => "282",
    ],
    [
      "cfr_id" => "fdc98307-5d03-4edb-af6a-3e15a79e5bb5",
      "name" => "Bugoma",
      "asset_id" => "322",
    ],
    [
      "cfr_id" => "a47b5587-4ff3-48d2-bbf2-01647cfe5224",
      "name" => "Bugomba",
      "asset_id" => "177",
    ],
    [
      "cfr_id" => "482a14e1-d997-4768-a11c-05123ecce8e6",
      "name" => "Bugondo Hill",
      "asset_id" => "299",
    ],
    [
      "cfr_id" => "25e1a0fc-ff2c-40f7-b2e9-05d614d43578",
      "name" => "Bugonzi",
      "asset_id" => "35",
    ],
    [
      "cfr_id" => "c8392c8c-114b-4fc8-b9ad-db0c45931e7d",
      "name" => "Bugusa",
      "asset_id" => "179",
    ],
    [
      "cfr_id" => "cfada954-f872-4167-bab6-cbc772cd23a9",
      "name" => "Buhungiro",
      "asset_id" => "132",
    ],
    [
      "cfr_id" => "6c4fec15-bd0d-45b8-bc0e-155fd7875e7d",
      "name" => "Bujawe",
      "asset_id" => "304",
    ],
    [
      "cfr_id" => "628f9b36-3493-4585-9822-dd77b59c3971",
      "name" => "Buka",
      "asset_id" => "5",
    ],
    [
      "cfr_id" => "4e80fbdb-6362-424f-81e9-741f4954095d",
      "name" => "Bukaibale",
      "asset_id" => "170",
    ],
    [
      "cfr_id" => "a9b206b3-df57-46de-981c-0cc34d21f645",
      "name" => "Bukakata",
      "asset_id" => "44",
    ],
    [
      "cfr_id" => "97e58d26-b582-4e8e-ba48-04ff609e34c6",
      "name" => "Bukaleba",
      "asset_id" => "139",
    ],
    [
      "cfr_id" => "89c58de2-3e90-492e-aeeb-cb409abebd2f",
      "name" => "Bukedea",
      "asset_id" => "324",
    ],
    [
      "cfr_id" => "f91b18ff-9184-4688-b20c-17488955a874",
      "name" => "Bukone",
      "asset_id" => "38",
    ],
    [
      "cfr_id" => "f517ab31-c47c-4ce9-adc4-1edb43ffc06a",
      "name" => "Bulijjo",
      "asset_id" => "133",
    ],
    [
      "cfr_id" => "fd0cc562-8490-4f0f-9704-975e174543d0",
      "name" => "Buloba",
      "asset_id" => "205",
    ],
    [
      "cfr_id" => "ce3bdfee-d945-4236-8a54-22033c29df3c",
      "name" => "Bulogo",
      "asset_id" => "358",
    ],
    [
      "cfr_id" => "a21c5a45-deb2-4280-9a2d-e2c9ed74a71a",
      "name" => "Bulondo",
      "asset_id" => "153",
    ],
    [
      "cfr_id" => "fa9daf99-b84f-44b4-be41-896e3b814186",
      "name" => "Buluku",
      "asset_id" => "188",
    ],
    [
      "cfr_id" => "2ce6b77a-38ed-4240-85b7-4a8ca304e7b5",
      "name" => "Bululu Hill",
      "asset_id" => "291",
    ],
    [
      "cfr_id" => "1ba4e912-98b7-45d7-b774-7259e59f9074",
      "name" => "Bumude-Nchwanga",
      "asset_id" => "239",
    ],
    [
      "cfr_id" => "2ed0e18d-35a2-4998-874a-f53f2ee94572",
      "name" => "Bundikeki",
      "asset_id" => "235",
    ],
    [
      "cfr_id" => "4b8b5114-984d-462d-8cd5-e3e5eb8ee266",
      "name" => "Bunjazi",
      "asset_id" => "53",
    ],
    [
      "cfr_id" => "b56efe3f-a75a-4fa6-92c9-a05279327594",
      "name" => "Busembatya",
      "asset_id" => "233",
    ],
    [
      "cfr_id" => "7fe7641f-8888-4a64-a18a-0eb90a057e6e",
      "name" => "Busowe",
      "asset_id" => "43",
    ],
    [
      "cfr_id" => "47d34ddd-2d60-49c0-8e3f-43dc0be213e7",
      "name" => "Butamira",
      "asset_id" => "267",
    ],
    [
      "cfr_id" => "43f199be-a327-4f10-a591-1a61729c5d85",
      "name" => "Buturume",
      "asset_id" => "80",
    ],
    [
      "cfr_id" => "1f4e775a-e558-4bf4-91c7-05fcb7af9469",
      "name" => "Buvuma",
      "asset_id" => "162",
    ],
    [
      "cfr_id" => "2462460f-00d4-4ff3-af38-e15c92b98c29",
      "name" => "Buwa",
      "asset_id" => "24",
    ],
    [
      "cfr_id" => "401f0c50-e4a3-45b6-a55b-9b6ff6e4efa0",
      "name" => "Buwaiswa",
      "asset_id" => "367",
    ],
    [
      "cfr_id" => "b2bbbde9-fbb7-484e-a977-9c7edc3d9022",
      "name" => "Buwanzi",
      "asset_id" => "169",
    ],
    [
      "cfr_id" => "9d68ff72-56ab-4dd1-b478-d6f4a90fa7a5",
      "name" => "Buyaga Dam",
      "asset_id" => "41",
    ],
    [
      "cfr_id" => "2b600972-eeb8-45bb-b2f1-99f500841b11",
      "name" => "Buyenvu",
      "asset_id" => "359",
    ],
    [
      "cfr_id" => "1232bc37-747e-4d3c-a890-ef0298ebfa1e",
      "name" => "Buziga",
      "asset_id" => "37",
    ],
    [
      "cfr_id" => "280d9ef2-eb1f-495e-bb27-5bcaacc660c3",
      "name" => "Bwambara",
      "asset_id" => "87",
    ],
    [
      "cfr_id" => "f8af0ba5-8e3c-4efe-92fc-927d5135e50d",
      "name" => "Bwezigolo-Gunga",
      "asset_id" => "223",
    ],
    [
      "cfr_id" => "fde974b6-b7e9-49ba-8cbe-965c1e3a9a3e",
      "name" => "Degeya",
      "asset_id" => "217",
    ],
    [
      "cfr_id" => "2f542d90-308f-4a9f-8160-500f6828ec82",
      "name" => "East Uru",
      "asset_id" => "494",
    ],
    [
      "cfr_id" => "66cf5c26-e2b1-4330-b1a7-7175158c63bf",
      "name" => "Echuya",
      "asset_id" => "119",
    ],
    [
      "cfr_id" => "43c361f2-9e5e-48f4-a79a-555e3e6c14f9",
      "name" => "Enjeva",
      "asset_id" => "463",
    ],
    [
      "cfr_id" => "3dd8d35e-1e84-40ac-b2be-f60cdf86878b",
      "name" => "Enyau",
      "asset_id" => "529",
    ],
    [
      "cfr_id" => "7efda596-e2b6-416a-9f41-d7cd9fe68870",
      "name" => "Epor",
      "asset_id" => "398",
    ],
    [
      "cfr_id" => "1243eb51-ba09-4481-9f5a-caebd9efbafd",
      "name" => "Era",
      "asset_id" => "512",
    ],
    [
      "cfr_id" => "3d748009-f033-428e-834e-fbb939f1b66f",
      "name" => "Eria",
      "asset_id" => "508",
    ],
    [
      "cfr_id" => "fe4dc145-be40-4a0f-a5cb-4775f651b1b7",
      "name" => "Fort Portal",
      "asset_id" => "255",
    ],
    [
      "cfr_id" => "87f76c13-14fd-45a9-a1c4-4a2d4974aa81",
      "name" => "Fumbya",
      "asset_id" => "296",
    ],
    [
      "cfr_id" => "7d4ad14d-6f7c-42d8-942b-3ac4dfbaaaa4",
      "name" => "Funve",
      "asset_id" => "73",
    ],
    [
      "cfr_id" => "452a7842-0507-40a7-b47d-1ba80d7e2422",
      "name" => "Gala",
      "asset_id" => "60",
    ],
    [
      "cfr_id" => "d321a24d-3f4d-452e-b0e0-a921abf6c60b",
      "name" => "Gangu",
      "asset_id" => "220",
    ],
    [
      "cfr_id" => "4b6accc1-269f-4b21-b726-4327ee310766",
      "name" => "Got-Gweno",
      "asset_id" => "462",
    ],
    [
      "cfr_id" => "37438516-0f15-4daa-bbd4-da9c530eb8a1",
      "name" => "Goyera",
      "asset_id" => "362",
    ],
    [
      "cfr_id" => "418d7689-98b3-40c0-a7ad-a608192dfac2",
      "name" => "Gulu",
      "asset_id" => "475",
    ],
    [
      "cfr_id" => "be93cd64-6481-438d-ba82-8559f719f66b",
      "name" => "Gung-Gung",
      "asset_id" => "391",
    ],
    [
      "cfr_id" => "3c3ca355-1fb3-4c71-b28c-9896a444c1e6",
      "name" => "Guramwa",
      "asset_id" => "347",
    ],
    [
      "cfr_id" => "56563453-12e6-402c-a3d5-027a91ffe2c5",
      "name" => "Gwengdiya",
      "asset_id" => "483",
    ],
    [
      "cfr_id" => "0c2dfe80-d292-44b3-91f0-99d05e5db520",
      "name" => "Gweri",
      "asset_id" => "430",
    ],
    [
      "cfr_id" => "570008b9-6cde-4156-8350-dc98a15fcdc0",
      "name" => "Ibamba",
      "asset_id" => "321",
    ],
    [
      "cfr_id" => "af69b7a4-e0a8-4863-a65f-ff93e274210e",
      "name" => "Ibambaro",
      "asset_id" => "266",
    ],
    [
      "cfr_id" => "69dcf03d-87d7-4cfa-a1d0-1d426774bb10",
      "name" => "Igwe",
      "asset_id" => "129",
    ],
    [
      "cfr_id" => "d2f88099-72f9-42d6-9a3d-ce5978d157ff",
      "name" => "Ihimbo",
      "asset_id" => "95",
    ],
    [
      "cfr_id" => "f604e788-3e5c-4530-a149-34e92878d003",
      "name" => "Ilera",
      "asset_id" => "409",
    ],
    [
      "cfr_id" => "c2e5e599-8b64-41a0-bfe9-ffcf3249d2b0",
      "name" => "Irimbi",
      "asset_id" => "136",
    ],
    [
      "cfr_id" => "ceb006a9-1f7a-4569-9aa5-978bea0d3ee2",
      "name" => "Itwara",
      "asset_id" => "371",
    ],
    [
      "cfr_id" => "a8ae6b6a-4123-4e84-ac8f-0dcbffdc341a",
      "name" => "Izinga Island",
      "asset_id" => "25",
    ],
    [
      "cfr_id" => "a8daa692-34fe-4a8d-8313-e45b0b4027eb",
      "name" => "Iziru",
      "asset_id" => "253",
    ],
    [
      "cfr_id" => "558e55a2-1f4a-4376-9796-b907f786a0e3",
      "name" => "Jubiya",
      "asset_id" => "32",
    ],
    [
      "cfr_id" => "52e759db-d2cf-4f70-ae9c-d139d2c7c2e3",
      "name" => "Jumbi",
      "asset_id" => "208",
    ],
    [
      "cfr_id" => "03af2618-d6b0-41a9-b959-bc02d874dd15",
      "name" => "Kabale",
      "asset_id" => "121",
    ],
    [
      "cfr_id" => "fb436a7c-5b80-45bb-8203-540696598e91",
      "name" => "Kabango-Muntandi",
      "asset_id" => "227",
    ],
    [
      "cfr_id" => "7a9d4f63-9b54-46d6-8da2-5529a67271ed",
      "name" => "Kabindo",
      "asset_id" => "366",
    ],
    [
      "cfr_id" => "6e3986bd-3915-40bd-be80-1af6d8dde643",
      "name" => "Kabira",
      "asset_id" => "101",
    ],
    [
      "cfr_id" => "03b7447d-e95a-4537-a8b0-0ec108a23f24",
      "name" => "Kabugeza (Kasanda)",
      "asset_id" => "125",
    ],
    [
      "cfr_id" => "61999d2e-a910-48c4-99a5-99b4170f33c3",
      "name" => "Kabukira",
      "asset_id" => "274",
    ],
    [
      "cfr_id" => "38b110dd-23b9-44cc-bcbe-d79ae0be636f",
      "name" => "Kabulego",
      "asset_id" => "13",
    ],
    [
      "cfr_id" => "e1685ac1-305a-4f35-a477-e6525571f011",
      "name" => "Kabuye",
      "asset_id" => "26",
    ],
    [
      "cfr_id" => "d9c7b49a-6663-4d6c-b2e0-d1769208b2c4",
      "name" => "Kabwika-Mujwalanganda",
      "asset_id" => "331",
    ],
    [
      "cfr_id" => "61ee497f-d182-4185-bea6-38451a57b6bb",
      "name" => "Kachogogweno",
      "asset_id" => "448",
    ],
    [
      "cfr_id" => "f3727455-1504-479c-8e42-aab2554bb5ae",
      "name" => "Kachung",
      "asset_id" => "434",
    ],
    [
      "cfr_id" => "a78f6945-55bf-45e7-9f92-c29d0a6122e4",
      "name" => "Kadam",
      "asset_id" => "444",
    ],
    [
      "cfr_id" => "549712f7-1aed-48c8-91f8-326b5b1c9cf4",
      "name" => "Kadre",
      "asset_id" => "521",
    ],
    [
      "cfr_id" => "e57d3b9d-35ff-4784-99f4-5eec268e5fe8",
      "name" => "Kaduku",
      "asset_id" => "451",
    ],
    [
      "cfr_id" => "748b1b8a-2f69-41c0-9622-0acd5d2cf461",
      "name" => "Kafu",
      "asset_id" => "477",
    ],
    [
      "cfr_id" => "264a22b1-dde9-450f-8061-755a475da3cb",
      "name" => "Kafumbi",
      "asset_id" => "182",
    ],
    [
      "cfr_id" => "b35e9088-46b5-4534-8657-f2de858a07f7",
      "name" => "Kagadi",
      "asset_id" => "351",
    ],
    [
      "cfr_id" => "a9954c69-ed8c-44b7-b95f-1b63090c94c3",
      "name" => "Kagogo",
      "asset_id" => "355",
    ],
    [
      "cfr_id" => "96a9a6c8-c10f-4527-ac25-59a36c3be568",
      "name" => "Kagoma",
      "asset_id" => "273",
    ],
    [
      "cfr_id" => "f08db1ef-508e-4679-8ca9-fee3f3460232",
      "name" => "Kagombe",
      "asset_id" => "353",
    ],
    [
      "cfr_id" => "f331d883-a674-44e7-aaa7-763a2d92085b",
      "name" => "Kagongo",
      "asset_id" => "215",
    ],
    [
      "cfr_id" => "ff514840-be22-4d46-905d-5bf82132b6b8",
      "name" => "Kagorra",
      "asset_id" => "364",
    ],
    [
      "cfr_id" => "43a61055-5670-41e2-a3ff-40bfda271fdc",
      "name" => "Kagwara",
      "asset_id" => "312",
    ],
    [
      "cfr_id" => "90ab4a43-bca0-4e65-9a6f-db2785bdbf75",
      "name" => "Kahurukobwire",
      "asset_id" => "326",
    ],
    [
      "cfr_id" => "bd9b31f3-ed5f-404a-9124-019e09c8d713",
      "name" => "Kaiso",
      "asset_id" => "115",
    ],
    [
      "cfr_id" => "9bc49081-d8d9-4603-bd1c-1b65015cfc1d",
      "name" => "Kajansi",
      "asset_id" => "195",
    ],
    [
      "cfr_id" => "71f8d99b-2fc6-47ae-bf9b-dd978bb5d9f1",
      "name" => "Kajonde",
      "asset_id" => "156",
    ],
    [
      "cfr_id" => "47f1fada-2cab-4c7c-a48e-0f18eba8b790",
      "name" => "Kakasi",
      "asset_id" => "29",
    ],
    [
      "cfr_id" => "ed0ec016-63be-40a4-a647-4e24d71c22ef",
      "name" => "Kakonwa",
      "asset_id" => "191",
    ],
    [
      "cfr_id" => "873661de-be8d-4d2d-be13-b9944add6cd0",
      "name" => "Kalagala Falls",
      "asset_id" => "275",
    ],
    [
      "cfr_id" => "0d514dbb-c164-4570-b2fb-d0fbbcf26d90",
      "name" => "Kalandazi",
      "asset_id" => "211",
    ],
    [
      "cfr_id" => "b93727dd-4a51-447b-a7f7-a2197c98bd7f",
      "name" => "Kalangalo",
      "asset_id" => "14",
    ],
    [
      "cfr_id" => "c6daf2e5-25f2-4433-8e9b-5ecd640e18d2",
      "name" => "Kalinzu",
      "asset_id" => "46",
    ],
    [
      "cfr_id" => "37eb91bb-f44b-414f-a134-930064a39006",
      "name" => "Kaliro",
      "asset_id" => "369",
    ],
    [
      "cfr_id" => "3cb455d2-27d0-4813-8f57-4c4c52715a5b",
      "name" => "Kalombi",
      "asset_id" => "209",
    ],
    [
      "cfr_id" => "3710bf4c-38cf-415f-8a58-ce4be1002439",
      "name" => "Kamera",
      "asset_id" => "34",
    ],
    [
      "cfr_id" => "51df5ee1-b824-41df-83f1-44e6e3fc2c39",
      "name" => "Kampala",
      "asset_id" => "42",
    ],
    [
      "cfr_id" => "ef9a5659-5996-445a-b1a6-432a22b640d6",
      "name" => "Kamukulu",
      "asset_id" => "75",
    ],
    [
      "cfr_id" => "8bf5db03-8663-4c30-ba5f-dd677e396890",
      "name" => "Kamusenene",
      "asset_id" => "333",
    ],
    [
      "cfr_id" => "100035cb-daf0-46f3-910f-7b1f0afba27c",
      "name" => "Kanaga",
      "asset_id" => "352",
    ],
    [
      "cfr_id" => "18878da6-1796-4786-b6e6-58fb351fefc8",
      "name" => "Kanangalo",
      "asset_id" => "222",
    ],
    [
      "cfr_id" => "7218a508-d296-46b3-ad7b-79d5f67269b3",
      "name" => "Kandanda-Ngobya",
      "asset_id" => "313",
    ],
    [
      "cfr_id" => "228a0877-e97f-40be-8c20-a6a9d9515b1b",
      "name" => "Kande",
      "asset_id" => "10",
    ],
    [
      "cfr_id" => "451500c6-c4db-45c3-99af-5e5b43e29b27",
      "name" => "Kaniabizo",
      "asset_id" => "98",
    ],
    [
      "cfr_id" => "65546296-831e-42c7-a1f0-0de1ac3d1e98",
      "name" => "Kanjaza",
      "asset_id" => "221",
    ],
    [
      "cfr_id" => "7ff0e09d-03c2-48ed-925f-e48244476359",
      "name" => "Kano",
      "asset_id" => "484",
    ],
    [
      "cfr_id" => "2f92f553-81e2-4370-b477-180618daf312",
      "name" => "Kapchorwa",
      "asset_id" => "323",
    ],
    [
      "cfr_id" => "e0b33853-85a4-4e51-8730-067ceff74594",
      "name" => "Kapimpini",
      "asset_id" => "339",
    ],
    [
      "cfr_id" => "d2968eff-f4c6-469a-b907-15c114e6b269",
      "name" => "Kasa",
      "asset_id" => "165",
    ],
    [
      "cfr_id" => "16b42817-9025-4d0f-9b76-c619ef9f9964",
      "name" => "Kasagala",
      "asset_id" => "329",
    ],
    [
      "cfr_id" => "0e62c1f2-bf0f-4696-90c8-38916a468027",
      "name" => "Kasala",
      "asset_id" => "174",
    ],
    [
      "cfr_id" => "47be8bf0-8953-4d9a-b613-cc7de7315838",
      "name" => "Kasana-Kasambya",
      "asset_id" => "287",
    ],
    [
      "cfr_id" => "906c03a7-7099-484e-9448-6bd871ce885b",
      "name" => "Kasato",
      "asset_id" => "334",
    ],
    [
      "cfr_id" => "4bf8b935-0ed7-4d86-8fd8-a72a773c10ec",
      "name" => "Kasega",
      "asset_id" => "370",
    ],
    [
      "cfr_id" => "6acd0bb2-e1b5-4ece-ad7c-e0b1ef2c4905",
      "name" => "Kasenyi",
      "asset_id" => "285",
    ],
    [
      "cfr_id" => "120aaa29-7101-4c00-8c2b-5a31b3622d63",
      "name" => "Kasokwa",
      "asset_id" => "289",
    ],
    [
      "cfr_id" => "a09bf910-cb82-49ee-a62b-66733f296b0c",
      "name" => "Kasolo",
      "asset_id" => "154",
    ],
    [
      "cfr_id" => "62277003-b067-4c42-a524-f2eaecc0e65c",
      "name" => "Kasongoire",
      "asset_id" => "303",
    ],
    [
      "cfr_id" => "35b9156e-f698-4013-8da9-7e2008846bfb",
      "name" => "Kasonke",
      "asset_id" => "47",
    ],
    [
      "cfr_id" => "c83a0efd-f3c2-4854-bb1a-f1b3de0c2932",
      "name" => "Kasozi",
      "asset_id" => "128",
    ],
    [
      "cfr_id" => "38aa8798-2bb2-44d3-a4d7-237fad67d889",
      "name" => "Kasyoha-Kitomi",
      "asset_id" => "31",
    ],
    [
      "cfr_id" => "be1bb6db-4abc-4d63-8055-5c9c407ed556",
      "name" => "Katabalalu",
      "asset_id" => "167",
    ],
    [
      "cfr_id" => "b0784643-3e6f-4625-9dee-1de8d5f8eb52",
      "name" => "Kateta",
      "asset_id" => "317",
    ],
    [
      "cfr_id" => "1903d0c4-8f0d-45be-9d16-9c5d86640775",
      "name" => "Katuugo",
      "asset_id" => "332",
    ],
    [
      "cfr_id" => "72278462-7b6a-4bd2-b76d-c711825093a5",
      "name" => "Kavunda",
      "asset_id" => "17",
    ],
    [
      "cfr_id" => "45937e01-2a56-4197-8f69-4cd6bce23581",
      "name" => "Kaweri",
      "asset_id" => "270",
    ],
    [
      "cfr_id" => "d0c5bad3-78f6-4244-935e-df420c2f210b",
      "name" => "Kazooba",
      "asset_id" => "2",
    ],
    [
      "cfr_id" => "fbcc3415-46d0-47f2-bfa2-9e0c64a0aec3",
      "name" => "Keyo",
      "asset_id" => "474",
    ],
    [
      "cfr_id" => "f8fbd20b-ce6c-4139-9781-0619ea1c58fb",
      "name" => "Kibego",
      "asset_id" => "231",
    ],
    [
      "cfr_id" => "72103574-16ad-47bb-8217-7f923c1ce474",
      "name" => "Kibeka",
      "asset_id" => "440",
    ],
    [
      "cfr_id" => "22fc4de0-8a37-491a-899f-175e6d09388c",
      "name" => "Kifu",
      "asset_id" => "135",
    ],
    [
      "cfr_id" => "0e2d0755-7972-4491-90da-14ba47339241",
      "name" => "Kifunvwe",
      "asset_id" => "181",
    ],
    [
      "cfr_id" => "33eb7ca8-11cc-49f3-b758-09f92b73e02b",
      "name" => "Kigona",
      "asset_id" => "103",
    ],
    [
      "cfr_id" => "4783d7d6-557d-4dfc-bc39-021f9237a2d0",
      "name" => "Kigona River",
      "asset_id" => "97",
    ],
    [
      "cfr_id" => "ca4f4a76-30e3-4f6d-8293-ea0ababfdf2a",
      "name" => "Kigulya Hill",
      "asset_id" => "458",
    ],
    [
      "cfr_id" => "a097ba31-f49c-4717-b761-c5f7a79877d2",
      "name" => "Kihaimira",
      "asset_id" => "363",
    ],
    [
      "cfr_id" => "c7190a76-6ffd-4c4c-88a7-948b08b3760f",
      "name" => "Kijanebalola",
      "asset_id" => "82",
    ],
    [
      "cfr_id" => "7140c728-1cdc-4e76-bdd3-084c394f3679",
      "name" => "Kijogolo",
      "asset_id" => "59",
    ],
    [
      "cfr_id" => "d6cf0460-8a21-4dca-8034-42c60f763af6",
      "name" => "Kijuna",
      "asset_id" => "343",
    ],
    [
      "cfr_id" => "e13d6edf-14a0-4233-92b6-cc5cd2266550",
      "name" => "Kijwiga",
      "asset_id" => "224",
    ],
    [
      "cfr_id" => "bbd9c005-53f1-4abf-9399-b0a7a12ccc9b",
      "name" => "Kikonda",
      "asset_id" => "327",
    ],
    [
      "cfr_id" => "c0bf5ce0-028c-49be-8ab1-73e7e534822e",
      "name" => "Kikumiro",
      "asset_id" => "238",
    ],
    [
      "cfr_id" => "bb1da7c5-1152-494f-8ecc-ce7f0cd47980",
      "name" => "Kilak",
      "asset_id" => "532",
    ],
    [
      "cfr_id" => "dddcfe10-de12-47b4-ae9d-78fcfe18a33e",
      "name" => "Kimaka",
      "asset_id" => "137",
    ],
    [
      "cfr_id" => "72ad45f9-39a4-4324-8141-e0a317a8707a",
      "name" => "Kinyo",
      "asset_id" => "203",
    ],
    [
      "cfr_id" => "56de3f53-8c67-44f2-9898-a53c912fcff4",
      "name" => "Kisakombe",
      "asset_id" => "210",
    ],
    [
      "cfr_id" => "cd8b62ab-42e5-4c7c-bee0-03793e540204",
      "name" => "Kisangi",
      "asset_id" => "164",
    ],
    [
      "cfr_id" => "c903f72e-f918-456e-91b1-28f672ae52ed",
      "name" => "Kisasa",
      "asset_id" => "49",
    ],
    [
      "cfr_id" => "6bbbf364-ccee-48bb-803d-f6acddf703da",
      "name" => "Kisisita (with Lubanga and Wangege)",
      "asset_id" => "213",
    ],
    [
      "cfr_id" => "07e51fef-42ff-408b-a230-cc9f660ce545",
      "name" => "Kisombwa",
      "asset_id" => "142",
    ],
    [
      "cfr_id" => "12ffd1b5-e249-46fb-8ee6-1ceaa303c3d3",
      "name" => "Kisubi FR",
      "asset_id" => "11",
    ],
    [
      "cfr_id" => "7fcf2f63-2405-4f94-b718-4a48b538c622",
      "name" => "Kitasi",
      "asset_id" => "69",
    ],
    [
      "cfr_id" => "23c13d41-1a3a-464e-a87a-b5768d2293a7",
      "name" => "Kitechura",
      "asset_id" => "234",
    ],
    [
      "cfr_id" => "446ba1bb-e747-49f8-b49c-4548b26d2aff",
      "name" => "Kitemu",
      "asset_id" => "61",
    ],
    [
      "cfr_id" => "c4dfe576-20dd-45db-b354-83c64d379c28",
      "name" => "Kitonya",
      "asset_id" => "250",
    ],
    [
      "cfr_id" => "8c219595-2049-4ac0-a5b3-0f9f5ca2acae",
      "name" => "Kitonya Hill",
      "asset_id" => "294",
    ],
    [
      "cfr_id" => "9a402b03-094d-4cb3-9bbd-2dd1af855afd",
      "name" => "Kitubulu",
      "asset_id" => "21",
    ],
    [
      "cfr_id" => "8b63963d-db2a-4a1a-a287-33312ad6b203",
      "name" => "Kiula",
      "asset_id" => "325",
    ],
    [
      "cfr_id" => "43b06e90-768a-44b1-8d0d-c84fb279995d",
      "name" => "Kizinkuba",
      "asset_id" => "212",
    ],
    [
      "cfr_id" => "d2be6d53-5867-4171-acef-99d957bc0b67",
      "name" => "Koja",
      "asset_id" => "6",
    ],
    [
      "cfr_id" => "412cde24-c361-4424-b637-03e3f7abd1b1",
      "name" => "Koko",
      "asset_id" => "183",
    ],
    [
      "cfr_id" => "01492311-1ea4-43d1-ad5e-2c6167f363f9",
      "name" => "Kubanda",
      "asset_id" => "48",
    ],
    [
      "cfr_id" => "1d10210b-8dcc-4599-8526-83340d6dabad",
      "name" => "Kulo-Obia",
      "asset_id" => "422",
    ],
    [
      "cfr_id" => "a03a3598-add0-4abd-900a-66b8d5c89e93",
      "name" => "Kulua",
      "asset_id" => "519",
    ],
    [
      "cfr_id" => "dd965612-a26f-4674-98cd-fc17e1947e69",
      "name" => "Kumbu (North)",
      "asset_id" => "52",
    ],
    [
      "cfr_id" => "cb29018c-0f0d-4fce-8f65-e048f321ade4",
      "name" => "Kumbu (South)",
      "asset_id" => "55",
    ],
    [
      "cfr_id" => "be01d6d1-448d-47bd-92c6-1b328aa1430d",
      "name" => "Kumi",
      "asset_id" => "310",
    ],
    [
      "cfr_id" => "ce1142f1-a5d3-4710-a013-3dbd0f90a468",
      "name" => "Kuzito",
      "asset_id" => "18",
    ],
    [
      "cfr_id" => "baa1e78e-26f4-41fc-8744-e055c043262c",
      "name" => "Kyabona",
      "asset_id" => "194",
    ],
    [
      "cfr_id" => "5444dbb0-04e6-460e-a044-9ceb6ad4c6e5",
      "name" => "Kyahaiguru",
      "asset_id" => "316",
    ],
    [
      "cfr_id" => "eb2cb315-0f70-422f-9965-7a8fb9b9d2a6",
      "name" => "Kyahi",
      "asset_id" => "81",
    ],
    [
      "cfr_id" => "7390b8c1-bff0-42bc-8d09-272569f60c18",
      "name" => "Kyalubanga",
      "asset_id" => "315",
    ],
    [
      "cfr_id" => "0cdff3b4-e1a9-4ad2-b832-42b1b1a8484a",
      "name" => "Kyalwamuka",
      "asset_id" => "88",
    ],
    [
      "cfr_id" => "19e23f5f-3d0e-4a4e-962c-a96c8c3b2345",
      "name" => "Kyamazzi",
      "asset_id" => "105",
    ],
    [
      "cfr_id" => "b053ef71-8362-44de-8ada-578bd3973b23",
      "name" => "Kyampisi",
      "asset_id" => "283",
    ],
    [
      "cfr_id" => "59b01d62-5270-41e4-bc24-5d445b3838c8",
      "name" => "Kyamugongo",
      "asset_id" => "308",
    ],
    [
      "cfr_id" => "20f77eea-b12b-41b5-bf1e-6005e211359c",
      "name" => "Kyamurangi",
      "asset_id" => "335",
    ],
    [
      "cfr_id" => "b103be16-9991-4a48-81c5-f4eef985d9db",
      "name" => "Kyansonzi",
      "asset_id" => "216",
    ],
    [
      "cfr_id" => "0e278045-8629-435a-afff-f8a2c8066d2c",
      "name" => "Kyantuhe",
      "asset_id" => "92",
    ],
    [
      "cfr_id" => "cee17acc-ba33-4b9b-a26c-e4ea55979bcf",
      "name" => "Kyehara",
      "asset_id" => "246",
    ],
    [
      "cfr_id" => "6e5a6c6d-d35b-446c-bb6c-a581c0f40eb1",
      "name" => "Kyewaga",
      "asset_id" => "22",
    ],
    [
      "cfr_id" => "da6fd142-2633-4f6f-adf6-58cf59c4a298",
      "name" => "Kyirira",
      "asset_id" => "68",
    ],
    [
      "cfr_id" => "ce34f59a-65aa-4d81-a3a0-96de6849f762",
      "name" => "Labala",
      "asset_id" => "538",
    ],
    [
      "cfr_id" => "ba8a3fc3-b350-447c-a709-ec7e72a2641d",
      "name" => "Lagute",
      "asset_id" => "468",
    ],
    [
      "cfr_id" => "4faa5222-d049-4135-b38a-3734c1f4a8c2",
      "name" => "Lajabwa",
      "asset_id" => "99",
    ],
    [
      "cfr_id" => "e0752584-32d1-4c95-9fdc-88724f02db7f",
      "name" => "Lalak",
      "asset_id" => "504",
    ],
    [
      "cfr_id" => "195fc80d-1b71-4318-8d65-2a2a3d1e50f3",
      "name" => "Lamwo",
      "asset_id" => "501",
    ],
    [
      "cfr_id" => "549c7373-66a1-4204-b078-8709d014d9e7",
      "name" => "Laura",
      "asset_id" => "473",
    ],
    [
      "cfr_id" => "f0ee2117-6a56-4dd4-90dc-4b56b8b7115b",
      "name" => "Lela-Olok",
      "asset_id" => "401",
    ],
    [
      "cfr_id" => "145084d8-fc43-41f4-86fa-2aceb1cb000f",
      "name" => "Lemutome",
      "asset_id" => "311",
    ],
    [
      "cfr_id" => "998027fe-433a-497a-b68c-6527b97529a3",
      "name" => "Lendu",
      "asset_id" => "387",
    ],
    [
      "cfr_id" => "0e816a01-9e1c-4e29-9637-79cfeff037bd",
      "name" => "Linga",
      "asset_id" => "83",
    ],
    [
      "cfr_id" => "2007990f-ced1-4b91-9a81-33e6c3c3b5cc",
      "name" => "Lira",
      "asset_id" => "411",
    ],
    [
      "cfr_id" => "bef11ea7-900b-4249-ad06-7c78eb742973",
      "name" => "Liru",
      "asset_id" => "520",
    ],
    [
      "cfr_id" => "479b6fd4-5ecd-4b6f-a8dc-938825630fc5",
      "name" => "Lobajo",
      "asset_id" => "507",
    ],
    [
      "cfr_id" => "f41be0e1-24ba-4fb8-ac0e-25cd49d0c056",
      "name" => "Lodonga",
      "asset_id" => "518",
    ],
    [
      "cfr_id" => "f72da9f2-2025-42b5-b1c1-31d4c9517ed8",
      "name" => "Lokiragodo",
      "asset_id" => "531",
    ],
    [
      "cfr_id" => "2acc6158-1ca4-4704-822b-c3c64aff6c87",
      "name" => "Lokung",
      "asset_id" => "509",
    ],
    [
      "cfr_id" => "dc193fd7-a566-45b7-8971-1645524d2e1c",
      "name" => "Lomej",
      "asset_id" => "485",
    ],
    [
      "cfr_id" => "7b5c5d7f-b990-499e-b6ee-a120b95aa37a",
      "name" => "Lotim-Puta",
      "asset_id" => "495",
    ],
    [
      "cfr_id" => "2c54fa44-bfbe-4a1d-bcd3-3181cee59f7b",
      "name" => "Lubani",
      "asset_id" => "263",
    ],
    [
      "cfr_id" => "4fdf7f66-5d87-48c1-a57d-868656a41c0f",
      "name" => "Lufuka",
      "asset_id" => "204",
    ],
    [
      "cfr_id" => "1a93fc5e-1d51-44ab-b4e5-bbef3da78c13",
      "name" => "Lukale",
      "asset_id" => "192",
    ],
    [
      "cfr_id" => "0419b6fc-8cc9-410c-b49c-257df5be3121",
      "name" => "Lukalu",
      "asset_id" => "77",
    ],
    [
      "cfr_id" => "b5814664-c1e9-4ae1-93a8-d0146ecff9d0",
      "name" => "Lukodi",
      "asset_id" => "466",
    ],
    [
      "cfr_id" => "9023c7c5-b8d0-4017-9e0a-7860be049028",
      "name" => "Lukolo",
      "asset_id" => "27",
    ],
    [
      "cfr_id" => "c4a951f2-8a7b-4899-92dc-231a938f1aa0",
      "name" => "Luku",
      "asset_id" => "469",
    ],
    [
      "cfr_id" => "3a00d445-3bd9-411a-b30a-a911195fa294",
      "name" => "Lukuga",
      "asset_id" => "259",
    ],
    [
      "cfr_id" => "ba5ae7a1-7349-43ee-a247-674289af9250",
      "name" => "Lul Kayonga",
      "asset_id" => "418",
    ],
    [
      "cfr_id" => "980dc300-861d-4559-bdb8-b90898bfb954",
      "name" => "Lul Oming",
      "asset_id" => "388",
    ],
    [
      "cfr_id" => "29075884-07a9-499d-9a81-f8f7f6d0dabd",
      "name" => "Lul Opio",
      "asset_id" => "392",
    ],
    [
      "cfr_id" => "d509bb12-5723-410c-9331-8153824a3d16",
      "name" => "Luleka",
      "asset_id" => "3",
    ],
    [
      "cfr_id" => "2b010b15-9ad5-4627-8a3a-e52a332fa301",
      "name" => "Lusiba",
      "asset_id" => "144",
    ],
    [
      "cfr_id" => "f7b3bde0-13b7-4c29-a798-f0407fe080b8",
      "name" => "Lutoboka",
      "asset_id" => "45",
    ],
    [
      "cfr_id" => "1e501178-c572-444e-8873-bfdad622333f",
      "name" => "Luvunya",
      "asset_id" => "143",
    ],
    [
      "cfr_id" => "5ba57844-885e-4bcd-970d-fb48e03b6114",
      "name" => "Luwafu",
      "asset_id" => "28",
    ],
    [
      "cfr_id" => "5f04720a-227f-4bde-be75-d6c0f655a274",
      "name" => "Luwawa",
      "asset_id" => "124",
    ],
    [
      "cfr_id" => "ab8091bf-57a0-4da7-9333-1594fca668d4",
      "name" => "Luwunga",
      "asset_id" => "349",
    ],
    [
      "cfr_id" => "db9ca625-4f14-46ac-9a2f-5437d240616e",
      "name" => "Luwungulu",
      "asset_id" => "63",
    ],
    [
      "cfr_id" => "bdc47ec8-da1a-4fe8-bdcc-718757adf967",
      "name" => "Lwala",
      "asset_id" => "498",
    ],
    [
      "cfr_id" => "35e22529-1838-4edc-986a-14bd9f1b26c2",
      "name" => "Lwamunda",
      "asset_id" => "152",
    ],
    [
      "cfr_id" => "103052cd-6790-470d-8052-842565e9bacc",
      "name" => "Mabira",
      "asset_id" => "278",
    ],
    [
      "cfr_id" => "59f8f72b-fb2f-4241-b4b5-3b114b0b65e7",
      "name" => "Mafuga",
      "asset_id" => "116",
    ],
    [
      "cfr_id" => "2fb1f057-ef9e-4b8f-80eb-e200d8ee4b0a",
      "name" => "Mako",
      "asset_id" => "218",
    ],
    [
      "cfr_id" => "86924360-7a33-49a3-a915-134bf88abd7f",
      "name" => "Makoko",
      "asset_id" => "74",
    ],
    [
      "cfr_id" => "8844fd69-491e-4121-9e7e-eed0d99302ab",
      "name" => "Makokolero",
      "asset_id" => "189",
    ],
    [
      "cfr_id" => "8b995052-51d0-419b-8197-f029a1d24c7e",
      "name" => "Mala Island",
      "asset_id" => "196",
    ],
    [
      "cfr_id" => "dff8100c-19bd-4377-b0f1-6dda2514b750",
      "name" => "Malabigambo",
      "asset_id" => "113",
    ],
    [
      "cfr_id" => "2cc4aa1e-47d4-4603-92a1-197bb3ee98ff",
      "name" => "Manwa (South East)",
      "asset_id" => "54",
    ],
    [
      "cfr_id" => "8cb23d11-496c-4bbf-aaa2-83f79f5f64bf",
      "name" => "Maruzi",
      "asset_id" => "449",
    ],
    [
      "cfr_id" => "03244f83-496b-4bd2-be29-5466278b5a93",
      "name" => "Masege",
      "asset_id" => "429",
    ],
    [
      "cfr_id" => "b7599e0c-bbfe-4053-bd30-bcba737c33ee",
      "name" => "Masindi",
      "asset_id" => "459",
    ],
    [
      "cfr_id" => "abbe4609-547b-4bf6-b507-e28f49b6d86c",
      "name" => "Mataa",
      "asset_id" => "240",
    ],
    [
      "cfr_id" => "5e4c4625-6323-4dd4-b417-b07664a06c10",
      "name" => "Matidi",
      "asset_id" => "526",
    ],
    [
      "cfr_id" => "66fd8c2f-2f3d-4c9d-8e05-34d2e2d27fd9",
      "name" => "Matiri",
      "asset_id" => "272",
    ],
    [
      "cfr_id" => "1b852fd2-6851-437b-9f34-3ffbe3c89712",
      "name" => "Mbale",
      "asset_id" => "344",
    ],
    [
      "cfr_id" => "42695287-e420-44f7-8695-1c349d5f794e",
      "name" => "Mbarara",
      "asset_id" => "90",
    ],
    [
      "cfr_id" => "ec568a18-6120-4d6c-8ea8-30b12c1e7603",
      "name" => "Mburamaizi",
      "asset_id" => "106",
    ],
    [
      "cfr_id" => "12962c9d-84f2-4065-98bd-f1f091afeaae",
      "name" => "Monikakinei",
      "asset_id" => "279",
    ],
    [
      "cfr_id" => "5f6a1a9d-55d4-461b-acc1-794f24ddbffc",
      "name" => "Morongole",
      "asset_id" => "534",
    ],
    [
      "cfr_id" => "a5f3240d-4fc4-42d3-a7e7-00aa6cadb43d",
      "name" => "Moroto",
      "asset_id" => "487",
    ],
    [
      "cfr_id" => "3d6b1790-9c9f-4fb8-93b5-69066255d112",
      "name" => "Mpanga",
      "asset_id" => "547",
    ],
    [
      "cfr_id" => "f9203d62-7094-472e-a17f-99f8a7e4b0a9",
      "name" => "Mpinve",
      "asset_id" => "228",
    ],
    [
      "cfr_id" => "1671db0f-bd2f-4306-9f83-a8a39fb0ba49",
      "name" => "Mt. Kei",
      "asset_id" => "464",
    ],
    [
      "cfr_id" => "614d4ce9-98e2-4bb6-b1b8-2d44cdc3ad9d",
      "name" => "Mubuku",
      "asset_id" => "168",
    ],
    [
      "cfr_id" => "68d34795-3da8-466e-a0b9-6a0010ba1967",
      "name" => "Mugomba",
      "asset_id" => "219",
    ],
    [
      "cfr_id" => "3a5c8369-d38e-4094-b2b4-c48104f089d1",
      "name" => "Mugoya",
      "asset_id" => "67",
    ],
    [
      "cfr_id" => "45a5afee-809d-49e2-86ce-7fe1a5690fbf",
      "name" => "Muhangi",
      "asset_id" => "354",
    ],
    [
      "cfr_id" => "c3ff4ee9-7425-422e-861a-c4a666fe8877",
      "name" => "Muhunga",
      "asset_id" => "361",
    ],
    [
      "cfr_id" => "628ae121-8d5e-4113-8bae-2710a5b359e5",
      "name" => "Muinaina",
      "asset_id" => "140",
    ],
    [
      "cfr_id" => "40d02d67-e230-46b3-9a0c-789c68ffd680",
      "name" => "Mujuzi",
      "asset_id" => "70",
    ],
    [
      "cfr_id" => "9d5d8bd7-c20e-4e97-9aa1-4b76029bc0a4",
      "name" => "Mukambwe",
      "asset_id" => "155",
    ],
    [
      "cfr_id" => "6ad41c5a-e4cf-4981-beb6-87276c34bb68",
      "name" => "Mukihani",
      "asset_id" => "302",
    ],
    [
      "cfr_id" => "1b60439e-c91c-418c-b7f0-31790e12fb12",
      "name" => "Muko",
      "asset_id" => "118",
    ],
    [
      "cfr_id" => "b854ba6b-a55c-4a32-8603-9fa2207924a4",
      "name" => "Mulenga",
      "asset_id" => "36",
    ],
    [
      "cfr_id" => "1d183714-ff9a-49c2-b8bd-129a313e49d6",
      "name" => "Mulundu",
      "asset_id" => "71",
    ],
    [
      "cfr_id" => "91fcb78b-7bec-4dec-9bf2-36b251def092",
      "name" => "Musamya",
      "asset_id" => "172",
    ],
    [
      "cfr_id" => "95270601-95f6-4a96-b046-50d3c9b588c3",
      "name" => "Musoma",
      "asset_id" => "309",
    ],
    [
      "cfr_id" => "574b82c9-ba18-44cf-adb6-219c6203b074",
      "name" => "Mutai",
      "asset_id" => "281",
    ],
    [
      "cfr_id" => "2a40aba6-bca5-48a8-944b-902654cea0b3",
      "name" => "Mwiri",
      "asset_id" => "130",
    ],
    [
      "cfr_id" => "8ce39313-5c54-4a22-97de-8cf5908f9300",
      "name" => "Mwola",
      "asset_id" => "4",
    ],
    [
      "cfr_id" => "358fcc7d-d369-49a4-adf6-58e8b95043df",
      "name" => "Nabanga",
      "asset_id" => "186",
    ],
    [
      "cfr_id" => "fd912ef4-6869-4585-a694-5002e0739e4f",
      "name" => "Nabukonge",
      "asset_id" => "65",
    ],
    [
      "cfr_id" => "7edab5d9-55dd-4e02-9c11-722ac37bd81a",
      "name" => "Nadagi",
      "asset_id" => "150",
    ],
    [
      "cfr_id" => "fcdc8708-1bba-4607-a425-20a6b40bc9d1",
      "name" => "Nagongera (East)",
      "asset_id" => "236",
    ],
    [
      "cfr_id" => "4d0359b2-a0ec-492e-b78a-dbcbe2fe58e8",
      "name" => "Nakaga",
      "asset_id" => "15",
    ],
    [
      "cfr_id" => "9563678b-87fb-47f2-9a44-d1938527dc0c",
      "name" => "Nakalanga",
      "asset_id" => "198",
    ],
    [
      "cfr_id" => "caecefb8-720f-4289-84d2-9b97d1c06214",
      "name" => "Nakalere",
      "asset_id" => "173",
    ],
    [
      "cfr_id" => "4e9215f4-27f9-4786-a573-e5d7f0a1a11a",
      "name" => "Nakawa Forestry Research",
      "asset_id" => "166",
    ],
    [
      "cfr_id" => "abef2d32-4718-44b2-a10c-3637313a9f08",
      "name" => "Nakaziba",
      "asset_id" => "178",
    ],
    [
      "cfr_id" => "6dcb2a4c-30fc-43b2-a54d-078328727902",
      "name" => "Nakindiba",
      "asset_id" => "141",
    ],
    [
      "cfr_id" => "f1ca82ae-9330-496c-81f0-d906ee53bb93",
      "name" => "Nakitondo",
      "asset_id" => "79",
    ],
    [
      "cfr_id" => "fab0d850-33a6-48ca-be0d-93a057ead5a1",
      "name" => "Nakiza",
      "asset_id" => "8",
    ],
    [
      "cfr_id" => "56bbeb4f-d37f-4160-b5da-16da5979c1e1",
      "name" => "Nakunyi",
      "asset_id" => "214",
    ],
    [
      "cfr_id" => "3be7c123-602d-44d6-be9e-78864ccf8578",
      "name" => "Nakuyazo",
      "asset_id" => "346",
    ],
    [
      "cfr_id" => "e275116f-bcc8-4637-83a8-d7bebcce1409",
      "name" => "Nakwaya",
      "asset_id" => "252",
    ],
    [
      "cfr_id" => "b0b638d9-ca8a-40b0-b4e6-a58c51049dae",
      "name" => "Nakwiga",
      "asset_id" => "372",
    ],
    [
      "cfr_id" => "a308f538-002f-4873-9ddf-b6bfc4aea33e",
      "name" => "Nalubaga",
      "asset_id" => "147",
    ],
    [
      "cfr_id" => "3d0548da-7b40-402e-93bb-5d2b689635e2",
      "name" => "Naludugavu",
      "asset_id" => "207",
    ],
    [
      "cfr_id" => "9dc69f9c-2340-4d98-b4e3-0c57420b3bd4",
      "name" => "Namabowe",
      "asset_id" => "1",
    ],
    [
      "cfr_id" => "30d13606-67d8-41b4-a346-0f40ef572b25",
      "name" => "Namafuma",
      "asset_id" => "126",
    ],
    [
      "cfr_id" => "64cfb2b5-4c45-4fda-9c68-8357b7488541",
      "name" => "Namakupa",
      "asset_id" => "271",
    ],
    [
      "cfr_id" => "d648cf87-d605-4def-a576-e150048bc390",
      "name" => "Namalala",
      "asset_id" => "111",
    ],
    [
      "cfr_id" => "3fa47930-ee1e-4f94-9db4-6b58df36cbaf",
      "name" => "Namalemba",
      "asset_id" => "357",
    ],
    [
      "cfr_id" => "e18ddb4f-eaeb-4895-8d3f-67c68d368e7d",
      "name" => "Namanve",
      "asset_id" => "546",
    ],
    [
      "cfr_id" => "6cd398f8-bead-4148-85ff-4827b1d21783",
      "name" => "Namasagali",
      "asset_id" => "341",
    ],
    [
      "cfr_id" => "848e20ff-51fd-4225-8990-065656d0b761",
      "name" => "Namasiga-Kidimbuli",
      "asset_id" => "280",
    ],
    [
      "cfr_id" => "2573cd19-d4e7-4a33-8937-d8f86056bf37",
      "name" => "Namatembe",
      "asset_id" => "66",
    ],
    [
      "cfr_id" => "74713bd5-58de-43dd-845d-9a30409d5173",
      "name" => "Namatiwa",
      "asset_id" => "199",
    ],
    [
      "cfr_id" => "df04991c-d58f-4b99-b6ae-2b6dc60cb6b6",
      "name" => "Namavundu",
      "asset_id" => "284",
    ],
    [
      "cfr_id" => "c4047383-040f-4f5a-a224-3f4c2bdf3a31",
      "name" => "Namawanyi & Namananga",
      "asset_id" => "122",
    ],
    [
      "cfr_id" => "23218115-120b-4ec5-bd81-f7548e851667",
      "name" => "Namazingiri",
      "asset_id" => "276",
    ],
    [
      "cfr_id" => "bbc9f639-4b38-4717-b926-af40cfaed8e1",
      "name" => "Nambale (Kasa South)",
      "asset_id" => "180",
    ],
    [
      "cfr_id" => "9675e3d4-b3d3-4894-9522-8755c6652e13",
      "name" => "Namwasa",
      "asset_id" => "241",
    ],
    [
      "cfr_id" => "cb157597-1f4a-4619-978a-96b32986979f",
      "name" => "Namyoya",
      "asset_id" => "148",
    ],
    [
      "cfr_id" => "b35b96a8-8e5c-412b-aebc-f34b91db4126",
      "name" => "Nanfuka",
      "asset_id" => "184",
    ],
    [
      "cfr_id" => "6f980582-0f63-4c7a-8f55-0ad233a35953",
      "name" => "Nangolibwel",
      "asset_id" => "488",
    ],
    [
      "cfr_id" => "22a2ad31-3d29-4888-a4e5-aa4b9b4733b0",
      "name" => "Napak",
      "asset_id" => "424",
    ],
    [
      "cfr_id" => "fc32bee0-4458-4996-87e4-b06fc2785631",
      "name" => "Napono",
      "asset_id" => "545",
    ],
    [
      "cfr_id" => "a86a4d0c-218e-4656-95f2-5230c27c50ce",
      "name" => "Natyonko",
      "asset_id" => "187",
    ],
    [
      "cfr_id" => "7effa171-12c2-45ca-a27a-6f1535951d67",
      "name" => "Navugulu",
      "asset_id" => "171",
    ],
    [
      "cfr_id" => "7b3fb8e1-1ff3-4111-8601-6c55ef994178",
      "name" => "Nawandigi",
      "asset_id" => "197",
    ],
    [
      "cfr_id" => "0a29d2e1-024e-44c8-beee-2ee68e2ee6f6",
      "name" => "Nfuka-Magobwa",
      "asset_id" => "229",
    ],
    [
      "cfr_id" => "825a74be-102e-4c9d-86a6-5aa3a37b1032",
      "name" => "Ngereka",
      "asset_id" => "251",
    ],
    [
      "cfr_id" => "616d9d85-4a74-4921-b7de-b2ae83d2b23b",
      "name" => "Ngeta",
      "asset_id" => "408",
    ],
    [
      "cfr_id" => "589dc608-feb8-4b49-ace8-9becb591e256",
      "name" => "Ngogwe (Bwema Island)",
      "asset_id" => "19",
    ],
    [
      "cfr_id" => "3af867ce-632c-4517-aa6a-24919a2fa248",
      "name" => "Nile Bank",
      "asset_id" => "262",
    ],
    [
      "cfr_id" => "43ed706f-6794-4d42-91d2-8c4d854173c9",
      "name" => "Nimu",
      "asset_id" => "23",
    ],
    [
      "cfr_id" => "82a5f717-932f-47bc-9c4f-07c189903732",
      "name" => "Nkera",
      "asset_id" => "258",
    ],
    [
      "cfr_id" => "5cc8cabc-180b-4a2e-9978-4463aa9b4a2e",
      "name" => "Nkese",
      "asset_id" => "51",
    ],
    [
      "cfr_id" => "f74d22ce-381b-4147-81b4-d1d4440470c0",
      "name" => "Nkogwe",
      "asset_id" => "7",
    ],
    [
      "cfr_id" => "e15ded75-85da-48cc-a281-66921f247a48",
      "name" => "Nkose",
      "asset_id" => "102",
    ],
    [
      "cfr_id" => "e0880651-9781-4f5e-b71e-db54728c7795",
      "name" => "Nonve",
      "asset_id" => "146",
    ],
    [
      "cfr_id" => "7678e761-b075-4858-a5d3-96b54892732e",
      "name" => "North Rwenzori",
      "asset_id" => "374",
    ],
    [
      "cfr_id" => "6b6b454d-4d42-4fdd-b381-764c2b55ade4",
      "name" => "Nsekuro Hill",
      "asset_id" => "298",
    ],
    [
      "cfr_id" => "87ee0787-04b3-47d0-819d-6d2c3794beb7",
      "name" => "Nsowe",
      "asset_id" => "159",
    ],
    [
      "cfr_id" => "dca003b0-c283-4e97-987d-bc95c48aa876",
      "name" => "Nsube",
      "asset_id" => "286",
    ],
    [
      "cfr_id" => "1fd2b02f-ec86-4227-8cab-090321f99afb",
      "name" => "Ntungamo",
      "asset_id" => "112",
    ],
    [
      "cfr_id" => "3e905ef5-6e99-4077-8b77-bea947bfff76",
      "name" => "Nyabiku",
      "asset_id" => "348",
    ],
    [
      "cfr_id" => "2e1447ef-8849-4258-8cce-c90cd94c3435",
      "name" => "Nyaburongo",
      "asset_id" => "244",
    ],
    [
      "cfr_id" => "2dc797c4-89fb-4c5b-be50-354529871a7a",
      "name" => "Nyabyeya",
      "asset_id" => "457",
    ],
    [
      "cfr_id" => "8efa8b1e-a0d7-4657-950b-0d81abfc6e12",
      "name" => "Nyakarongo",
      "asset_id" => "368",
    ],
    [
      "cfr_id" => "516f8453-eeb2-4692-9afc-0d4ae8cfbf8d",
      "name" => "Nyakunyu",
      "asset_id" => "293",
    ],
    [
      "cfr_id" => "57305afe-f618-4d6e-8f51-722f9f3db586",
      "name" => "Nyamakere",
      "asset_id" => "431",
    ],
    [
      "cfr_id" => "951d5513-8d5b-4186-9380-37d55cc3ef82",
      "name" => "Nyangea-Napore",
      "asset_id" => "511",
    ],
    [
      "cfr_id" => "fb5e101f-1fc9-48ff-b32e-9ee51ba0648e",
      "name" => "Obel",
      "asset_id" => "402",
    ],
    [
      "cfr_id" => "5cf55eff-1ce4-4afd-b559-2d1685b70173",
      "name" => "Ocamo-Lum",
      "asset_id" => "301",
    ],
    [
      "cfr_id" => "31afdb56-ad11-4712-910b-4b899e88ae88",
      "name" => "Ochomai",
      "asset_id" => "433",
    ],
    [
      "cfr_id" => "f8de10d5-b9be-44bc-971d-38ecc6aaf729",
      "name" => "Ochomil",
      "asset_id" => "307",
    ],
    [
      "cfr_id" => "24bb035e-4b36-4513-b67c-c46dbba20e61",
      "name" => "Ogera Hill",
      "asset_id" => "305",
    ],
    [
      "cfr_id" => "7b324595-809e-43de-9743-4f0f9427c481",
      "name" => "Ogili",
      "asset_id" => "528",
    ],
    [
      "cfr_id" => "8d0df2f0-1734-49de-88eb-741c2f3d625f",
      "name" => "Ogom",
      "asset_id" => "537",
    ],
    [
      "cfr_id" => "0e93ca78-2c7a-4444-9655-06079d19d099",
      "name" => "Ogur",
      "asset_id" => "394",
    ],
    [
      "cfr_id" => "5cc72299-2cf4-4efe-a099-bd32abf3122b",
      "name" => "Ojwiting",
      "asset_id" => "377",
    ],
    [
      "cfr_id" => "1b7dbacb-a76e-4932-aec9-8d4e1e8dcb83",
      "name" => "Okavu-Reru",
      "asset_id" => "490",
    ],
    [
      "cfr_id" => "190aa2e8-ffdb-4d59-8993-6f35120c7c55",
      "name" => "Okurango",
      "asset_id" => "414",
    ],
    [
      "cfr_id" => "364fbf44-89d7-4203-a622-645332fb1599",
      "name" => "Olamusa",
      "asset_id" => "12",
    ],
    [
      "cfr_id" => "9ffa21a6-100f-42ed-bc37-ebb256030d07",
      "name" => "Olia",
      "asset_id" => "386",
    ],
    [
      "cfr_id" => "684c08cd-058d-4846-ade4-2c1ca98075b9",
      "name" => "Oliduro",
      "asset_id" => "390",
    ],
    [
      "cfr_id" => "0b5f909a-ee50-4afd-ac8e-5b33f4024953",
      "name" => "Olwal",
      "asset_id" => "470",
    ],
    [
      "cfr_id" => "855e7a02-136f-4b29-b1ec-9b61b2830c63",
      "name" => "Omier",
      "asset_id" => "379",
    ],
    [
      "cfr_id" => "36a478c0-3e40-4344-80b4-8c366233249f",
      "name" => "Onekokeo",
      "asset_id" => "443",
    ],
    [
      "cfr_id" => "09492a71-36c4-4068-9bca-bc16f111c3ad",
      "name" => "Ongom",
      "asset_id" => "412",
    ],
    [
      "cfr_id" => "9b53cce4-da35-4e5d-b84b-167c1e19e96e",
      "name" => "Onyurut",
      "asset_id" => "454",
    ],
    [
      "cfr_id" => "ab29475f-5ba5-49af-9f08-faebdbc26ffd",
      "name" => "Opaka",
      "asset_id" => "489",
    ],
    [
      "cfr_id" => "311f35b6-9a3a-4358-997b-e9c3a17fbb64",
      "name" => "Opit",
      "asset_id" => "376",
    ],
    [
      "cfr_id" => "3b50b279-9c05-498c-922f-fd4a0f06c8de",
      "name" => "Opok",
      "asset_id" => "478",
    ],
    [
      "cfr_id" => "238dccb0-732b-4596-b8b4-10ff038aecf5",
      "name" => "Oruha",
      "asset_id" => "257",
    ],
    [
      "cfr_id" => "4b71d855-4076-469a-99c0-5c3e13bacce1",
      "name" => "Otrevu",
      "asset_id" => "527",
    ],
    [
      "cfr_id" => "8018594d-4c01-4489-8dc1-5e135f817705",
      "name" => "Otukei",
      "asset_id" => "383",
    ],
    [
      "cfr_id" => "67130634-db67-4d25-a1a4-a392951ac10d",
      "name" => "Otzi (East)",
      "asset_id" => "540",
    ],
    [
      "cfr_id" => "503489c6-52c4-4c53-be0e-3df18fdef2f2",
      "name" => "Otzi (West)",
      "asset_id" => "506",
    ],
    [
      "cfr_id" => "a72e68d3-5ff2-4363-a302-3a6d755ce249",
      "name" => "Ozubu",
      "asset_id" => "513",
    ],
    [
      "cfr_id" => "09d3335d-2d81-4204-809e-ac4ef3022278",
      "name" => "Pajimu",
      "asset_id" => "522",
    ],
    [
      "cfr_id" => "90c5b3ab-a2cb-4b6b-a44b-9d3f123719ce",
      "name" => "Paonyeme",
      "asset_id" => "514",
    ],
    [
      "cfr_id" => "b1d252b5-5b8f-4674-ac5a-89a3536dbcae",
      "name" => "Parabongo",
      "asset_id" => "533",
    ],
    [
      "cfr_id" => "b05e61f0-f94e-4090-bfae-d732d8e21b73",
      "name" => "Pokoli",
      "asset_id" => "226",
    ],
    [
      "cfr_id" => "314773b7-dc44-47c0-9174-598bb5bc2a29",
      "name" => "Rom",
      "asset_id" => "515",
    ],
    [
      "cfr_id" => "d14e5518-1538-44a5-bbd7-d72728228977",
      "name" => "Rugongi",
      "asset_id" => "33",
    ],
    [
      "cfr_id" => "87af2539-a946-4298-8c39-746df7f5024e",
      "name" => "Rukara",
      "asset_id" => "356",
    ],
    [
      "cfr_id" => "223ebf92-f059-4ca8-af14-0d49d6e0857b",
      "name" => "Rukungiri",
      "asset_id" => "107",
    ],
    [
      "cfr_id" => "bf2d5f9c-8e27-41f5-b49d-0c7db0a5a968",
      "name" => "Rushaya",
      "asset_id" => "86",
    ],
    [
      "cfr_id" => "ba6ba2c4-cb39-4f60-a23c-e8362d55653a",
      "name" => "Ruzaire",
      "asset_id" => "340",
    ],
    [
      "cfr_id" => "0f845a2b-3d88-4aa8-94ed-b4644f4f6364",
      "name" => "Rwengeye",
      "asset_id" => "336",
    ],
    [
      "cfr_id" => "a1d656ff-eac7-4492-80fd-63897e7e427e",
      "name" => "Rwengiri",
      "asset_id" => "96",
    ],
    [
      "cfr_id" => "ea06c643-b034-4565-9ee4-58276ee15d12",
      "name" => "Rwensama",
      "asset_id" => "288",
    ],
    [
      "cfr_id" => "64b8b734-1b36-4f50-80d2-b0527c3c3570",
      "name" => "Rwensambya",
      "asset_id" => "138",
    ],
    [
      "cfr_id" => "a3c80cd9-d207-41d2-997c-978236e79796",
      "name" => "Rwoho",
      "asset_id" => "109",
    ],
    [
      "cfr_id" => "2faf8a80-2d65-41bf-8b6b-f1e851986600",
      "name" => "Sala",
      "asset_id" => "342",
    ],
    [
      "cfr_id" => "c94421a4-64e9-4eab-b891-7d0eacb2508a",
      "name" => "Sambwa",
      "asset_id" => "319",
    ],
    [
      "cfr_id" => "8f969af1-dbfe-48d0-88c2-0e5ecdec1c81",
      "name" => "Sekazinga",
      "asset_id" => "76",
    ],
    [
      "cfr_id" => "a3a2c494-6965-4a29-b028-f16fbc9267c2",
      "name" => "Semunya",
      "asset_id" => "206",
    ],
    [
      "cfr_id" => "ba80ff47-c1ea-4305-ba40-1ed76e7dc521",
      "name" => "Sirisiri",
      "asset_id" => "292",
    ],
    [
      "cfr_id" => "a43f27d1-d417-46b8-9c72-26134a159c25",
      "name" => "Sitambogo",
      "asset_id" => "127",
    ],
    [
      "cfr_id" => "5c1f7b7c-0513-404a-a56c-2a6f5ef827c8",
      "name" => "Soroti",
      "asset_id" => "452",
    ],
    [
      "cfr_id" => "72711347-7933-41c1-9d77-63161498eb0e",
      "name" => "South Busoga",
      "asset_id" => "157",
    ],
    [
      "cfr_id" => "ac9e6e4f-de24-4895-889b-2a7a1b74ef6a",
      "name" => "South Maramagambo",
      "asset_id" => "541",
    ],
    [
      "cfr_id" => "fef71ee1-315a-4f6b-8313-6e6f9976be34",
      "name" => "Sozi",
      "asset_id" => "175",
    ],
    [
      "cfr_id" => "c1bc4d79-5b82-44de-9015-0be5842f670c",
      "name" => "Suru",
      "asset_id" => "535",
    ],
    [
      "cfr_id" => "b6ce0242-4252-4485-a5a0-4b36d99a9fb1",
      "name" => "Taala",
      "asset_id" => "350",
    ],
    [
      "cfr_id" => "2ddf3ba6-2f37-46a9-879d-142c6ea3ffc4",
      "name" => "Tebakoli",
      "asset_id" => "232",
    ],
    [
      "cfr_id" => "2872ebe4-c8cd-4c59-80f0-1b773251af4c",
      "name" => "Telwa",
      "asset_id" => "415",
    ],
    [
      "cfr_id" => "9926897a-1c10-4f5c-9f88-5dde8704f66c",
      "name" => "Tero (East)",
      "asset_id" => "108",
    ],
    [
      "cfr_id" => "b6828916-ba5b-4fbf-a827-6948b4d6c590",
      "name" => "Tero (West)",
      "asset_id" => "110",
    ],
    [
      "cfr_id" => "a8bdcaf1-c663-446a-b948-6de8494530d7",
      "name" => "Timu",
      "asset_id" => "505",
    ],
    [
      "cfr_id" => "67d04c33-072f-42dc-b67b-39079f45203f",
      "name" => "Tonde",
      "asset_id" => "64",
    ],
    [
      "cfr_id" => "a3c3756a-15d3-4af4-b7b8-71f5e957f2c8",
      "name" => "Tororo",
      "asset_id" => "242",
    ],
    [
      "cfr_id" => "e550b0ce-1390-4369-af98-86e63bb3c9fc",
      "name" => "Towa",
      "asset_id" => "57",
    ],
    [
      "cfr_id" => "63072f12-75c9-41cc-91cf-d59152a5342e",
      "name" => "Tumbi",
      "asset_id" => "145",
    ],
    [
      "cfr_id" => "13942401-1fd4-4b42-a5a9-a45b4b9e78cb",
      "name" => "Usi",
      "asset_id" => "381",
    ],
    [
      "cfr_id" => "8edf0169-55c6-4bbf-92c5-ec2951c8b5e0",
      "name" => "Wabinyomo",
      "asset_id" => "201",
    ],
    [
      "cfr_id" => "cfab3b18-cf5d-4cf1-9948-c5c7abb19a25",
      "name" => "Wabisi-Wajala",
      "asset_id" => "314",
    ],
    [
      "cfr_id" => "419ca9f3-ec93-465b-8f8b-1e067ce1568c",
      "name" => "Wabitembe",
      "asset_id" => "62",
    ],
    [
      "cfr_id" => "fca4cb5b-73ac-4b3e-b592-6eb7a31639f5",
      "name" => "Wadelai",
      "asset_id" => "480",
    ],
    [
      "cfr_id" => "ed34257b-b97a-40b3-856b-3879115e027d",
      "name" => "Wakayembe",
      "asset_id" => "149",
    ],
    [
      "cfr_id" => "b2dc9146-a5c5-4b0c-9db6-1652dd21b2a3",
      "name" => "Walugogo",
      "asset_id" => "268",
    ],
    [
      "cfr_id" => "daf53a1f-b466-41d0-a6fa-ae699cee582d",
      "name" => "Walugondo",
      "asset_id" => "151",
    ],
    [
      "cfr_id" => "2de95283-947c-4c56-915f-64df778bdea8",
      "name" => "Walulumbu",
      "asset_id" => "134",
    ],
    [
      "cfr_id" => "658acc7e-d0f0-4080-ab87-b5058dcfdc73",
      "name" => "Walumwanyi",
      "asset_id" => "190",
    ],
    [
      "cfr_id" => "1cfa9520-480d-4e5d-b17a-9acba09da89d",
      "name" => "Wamale",
      "asset_id" => "328",
    ],
    [
      "cfr_id" => "13bc12e0-0899-4c72-851a-8b265cf416a5",
      "name" => "Wamasega",
      "asset_id" => "200",
    ],
    [
      "cfr_id" => "eece12b8-a633-4974-bc44-f80c9a33a128",
      "name" => "Wambabya",
      "asset_id" => "318",
    ],
    [
      "cfr_id" => "dc398485-274f-41f6-9400-bfa5b62bae59",
      "name" => "Wangu",
      "asset_id" => "360",
    ],
    [
      "cfr_id" => "baa42324-10a4-47a2-8240-a333c2d4d620",
      "name" => "Wankweyo",
      "asset_id" => "345",
    ],
    [
      "cfr_id" => "de9552d0-40e9-416a-8072-ba6a859d1eae",
      "name" => "Wantagalala",
      "asset_id" => "9",
    ],
    [
      "cfr_id" => "1d2b02b9-edd7-4815-8daf-d5e7cf970547",
      "name" => "Wantayi",
      "asset_id" => "158",
    ],
    [
      "cfr_id" => "cd8758a9-c4ff-4d2e-998e-f2ccfb8054ae",
      "name" => "Wati",
      "asset_id" => "524",
    ],
    [
      "cfr_id" => "b0cb20ee-27d6-4799-9f31-b0be667e860b",
      "name" => "West Bugwe",
      "asset_id" => "123",
    ],
    [
      "cfr_id" => "ec572c8f-b284-48d1-b2d6-aa0c356695fa",
      "name" => "West Uru",
      "asset_id" => "491",
    ],
    [
      "cfr_id" => "97058d2d-0180-4c12-a2d6-a33c3938aafa",
      "name" => "Wiceri",
      "asset_id" => "539",
    ],
    [
      "cfr_id" => "9ad6c0f1-ed3c-4d06-9ba0-3cb2b7772e87",
      "name" => "Yubwe",
      "asset_id" => "193",
    ],
    [
      "cfr_id" => "6349f568-1fc3-4505-9039-8eb7ad4db134",
      "name" => "Zimwa",
      "asset_id" => "373",
    ],
    [
      "cfr_id" => "76b13922-d6e1-40c7-bdd5-01e01233d848",
      "name" => "Zirimiti",
      "asset_id" => "176",
    ],
    [
      "cfr_id" => "1f25dcbe-2d13-47b4-a685-9ec1235ba346",
      "name" => "Zoka",
      "asset_id" => "530",
    ],
    [
      "cfr_id" => "3f032eaf-09d8-43b7-87fc-140d64004431",
      "name" => "Zulia",
      "asset_id" => "499",
    ],
  ];

  if (!isset($sandbox['progress'])) {
    $sandbox['cfrs'] = $cfrs;
    $sandbox['max'] = count($sandbox['cfrs']);
    $sandbox['progress'] = 0;
    $sandbox['steps'] = Settings::get('entity_update_batch_size', 25);
  }

  $cfrs = array_slice($sandbox['cfrs'], $sandbox['progress'], $sandbox['steps']);
  foreach ($cfrs as $cfr) {
    $cfr_id = $cfr['cfr_id'];
    $name = $cfr['name'];
    $asset_id = $cfr['asset_id'];
    $asset = \Drupal::entityTypeManager()->getStorage('asset')->loadByProperties(['id' => $asset_id]);
    if (!empty($asset)) {
      // Run sanity checks and log a warning if the CFR names don't match and
      // if the asset already has a CFR ID.
      $asset = reset($asset);
      if (!empty($asset->cfr_global_id)) {
        \Drupal::logger('Farm NFA')->warning('Asset @asset_id already has a CFR ID @cfr_id', ['@asset_id' => $asset_id, '@cfr_id' => $cfr_id]);
      }
      if ($asset->getName() != $name) {
        \Drupal::logger('Farm NFA')->warning('Asset @asset_name and BRMS CFR name @cfr_name are not the same.', ['@asset_name' => $asset->getName(), '@cfr_name' => $name]);
      }
      $asset->cfr_global_id = $cfr_id;
      $asset->save();
    }
    else {
      \Drupal::logger('Farm NFA')->warning('No asset found for CFR ID @cfr_id', ['@cfr_id' => $cfr_id]);
    }
    $sandbox['progress']++;
  }
  $sandbox['#finished'] = empty($sandbox['max']) ? 1 : ($sandbox['progress'] / $sandbox['max']);

  \Drupal::messenger()->addMessage(t('Updated @progress of @max CFRs.',
    ['@progress' => $sandbox['progress'], '@max' => $sandbox['max']]));
}

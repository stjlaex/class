<?xml version="1.0" encoding="windows-1252"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml" xmlns:set="http://exslt.org/sets">
<!-- -->

<xsl:output method="html"/>



<xsl:template match="admissioncenters">

<div id="template-content">
  <div class="head">
	<div class="left">
	  <img src="../images/classisicon.png" onclick="admission_chart('current',2016,'','');"/>
	  <input type="hidden" id="centerpassword">
	    <xsl:attribute name="value">
		  <xsl:value-of select="/admissioncenters/admissioncenter[1]/stats/school/password"/>
	    </xsl:attribute>
	  </input>
	</div>
	<div>
	</div>
	<div class="right">
	  <xsl:value-of select="datestamp"/>
	  <div id="admissionsprint" style="display:none;">
	     <div id="calendar-admissionsprintdate">
	  		<input type="date" id="admissionsprintdate" placeholder="Date. Example: 2014-06-02"/>
	  		<img class="calendar"/>
	  	</div>
	  	<button id="admissionsprintbutton">Enrolments report</button>
	  </div>
	</div>
  </div>

  <div class="spacer"/>

  <div id="chart" class="center"/>

  <div class="spacer"/>

  <xsl:apply-templates select="admissioncenter">
  </xsl:apply-templates>
</div>
  <hr/>

</xsl:template>



<xsl:template match="admissioncenter">
  <xsl:variable name="currentyear" select="2016"/>
  <xsl:variable name="nextyear" select="2017"/>

  <xsl:call-template name="enrolcurrent">
	<xsl:with-param name="cols" select="set:distinct(stats/tables/table[name='enrolcurrent']/cols/col/value)"/>
	<xsl:with-param name="year" select="$currentyear"/>
  </xsl:call-template>


  <xsl:call-template name="enrolcurrent">
	<xsl:with-param name="cols" select="set:distinct(stats/tables/table[name='enrolcurrent']/cols/col/value)"/>
	<xsl:with-param name="year" select="$nextyear"/>
  </xsl:call-template>

</xsl:template>



<!-- Enrol Current table once per admission center -->
<xsl:template name="enrolcurrent">
  <xsl:param name="cols"/>
  <xsl:param name="year"/>


  <div class="hidden">
	<xsl:element name="table">
	  <xsl:attribute name="id"><xsl:value-of select="stats/school/value"/><xsl:value-of select="$year"/></xsl:attribute>
	  <tr>
		<td class="centername"><xsl:value-of select="stats/school/value"/></td>
		<td class="enrolyear"><xsl:value-of select="$year"/></td>
		<td class="centerurl"><xsl:value-of select="stats/school/url"/></td>
	  </tr>
	  <tr>
		<th><label/></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stats/stat[enrolyear/value=$year]/groups/group[type='year' or not(type)]">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label/></th>
		<xsl:call-template name="headerrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		</xsl:call-template>
	  </tr>

	  <xsl:apply-templates select="stats/stat[enrolyear/value=$year]/groups/group[type!='year']">
		<xsl:with-param name="index" select="0"/>
		<xsl:with-param name="cols" select="$cols"/>
	  </xsl:apply-templates>

	  <tr>
		<th><label>TOTAL</label></th>
		<xsl:call-template name="totalrow">
		  <xsl:with-param name="index" select="1"/>
		  <xsl:with-param name="cols" select="$cols"/>
		  <xsl:with-param name="year" select="$year"/>
		</xsl:call-template>
	  </tr>
	</xsl:element>
  </div>

</xsl:template>





<xsl:template match="group">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"/>

  <tr>
	<th class="xlabel">
	  <xsl:value-of select="name"/>
	</th>			
	<xsl:call-template name="cell">
	  <xsl:with-param name="index" select="1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>
  </tr>

</xsl:template>





<xsl:template name="cell">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"/>

  <xsl:variable name="maxindex" select="count($cols)"/>

  <xsl:if test="$index &lt; $maxindex+1">
	<xsl:variable name="code"><xsl:value-of select="$cols[$index]"/>:<xsl:value-of select="id"/></xsl:variable>

	<xsl:element name="td">
	  <xsl:attribute name="class"><xsl:value-of select="substring-before($code,':')"/></xsl:attribute>
	  <xsl:value-of select="number[name=$code]/value"/>
	</xsl:element>

    <xsl:call-template name="cell">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>
  </xsl:if>
</xsl:template>




<xsl:template name="headerrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"/>

  <xsl:variable name="maxindex" select="count($cols)"/>

  <xsl:if test="$index &lt; $maxindex+1">

	<th>
	  <xsl:value-of select="stats/tables/table/cols/col[value=$cols[$index]]/name"/>
	  <xsl:if test="tables/table/cols/col[value=$cols[$index]]/date">
		<br/><label><xsl:value-of select="tables/table/cols/col[value=$cols[$index]]/date"/></label>
	  </xsl:if>
	</th>
  
	<xsl:call-template name="headerrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>



<xsl:template name="totalrow">
  <xsl:param name="index">1</xsl:param>
  <xsl:param name="cols"/>
  <xsl:param name="year"/>

  <xsl:variable name="maxindex" select="count($cols)"/>

  <xsl:if test="$index &lt; $maxindex+1">
	<xsl:variable name="code"><xsl:value-of select="$cols[$index]"/>:</xsl:variable>

	<xsl:element name="td">
	  <xsl:attribute name="class">total<xsl:value-of select="substring-before($code,':')"/></xsl:attribute>
	  <xsl:value-of select="sum(stats/stat[enrolyear/value=$year]/groups/group[type='year' or not(type)]/number[contains(name,$code)]/value)"/>
	</xsl:element>

	<xsl:call-template name="totalrow">
	  <xsl:with-param name="index" select="$index+1"/>
	  <xsl:with-param name="cols" select="$cols"/>
	  <xsl:with-param name="year" select="$year"/>
	</xsl:call-template>

  </xsl:if>

</xsl:template>

</xsl:stylesheet>

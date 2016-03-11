<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
<!-- -->


<xsl:output method="html" />

<xsl:template match="students">
  <xsl:param name="bid"><xsl:value-of select="component/value_db" /></xsl:param>

  <div class="head">
	  <label>Tracking Grid:</label>
	  <xsl:value-of select="subject/value" />
	  <xsl:text> </xsl:text>
	  <xsl:value-of select="component/value" />

	  <label>Classes:</label>
	  <xsl:value-of select="description/value" />
  </div>

  <div id="panel" class="center">
	<p style="padding-left:5em;font-weight:100;font-style:italic;">
	  (Click the dots to display student names.)
	</p>
  </div>
  <div id="chart" class="center"></div>

  <div class="hidden">
	<table id="graph">
	  <tfoot id="graph_axis">
		<tr>
		  <td> </td>
		  <xsl:call-template name="resheader" />
		</tr>
	  </tfoot>
	  <tbody id="graph_data">
		<xsl:call-template name="assrow">
		  <xsl:with-param name="assindex" select="1"/>
		  <xsl:with-param name="bid" select="$bid" />
		</xsl:call-template>
	  </tbody>
	</table>

	<ul class="hidden">  
	  <xsl:apply-templates select="student" />
	</ul>
  </div>
  <div class='spacer'></div>

<hr />

</xsl:template>



<xsl:template name="resheader">
  <xsl:param name="index">1</xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(restable/res/label)+1"/>
  </xsl:variable>
  <xsl:variable name='reslabel' select='restable/res/label' />
  <xsl:if test="$index &lt; $maxindex">
	<th>
		<xsl:value-of select="$reslabel[$index]" />  
	</th>

    <xsl:call-template name="resheader">
	  <xsl:with-param name="index" select="$index + 1"/>
    </xsl:call-template>

  </xsl:if>
</xsl:template>



<xsl:template name="assrow">
  <xsl:param name="assindex">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(asstable/ass/id_db)+1"/>
  </xsl:variable>
  <xsl:variable name="asses" select="asstable/ass" />
  <xsl:if test="$assindex &lt; $maxindex">

	<tr>
	  <th scope="row">
		<xsl:value-of select="$asses[$assindex]/date" />
		<xsl:call-template name="averagerow">
		  <xsl:with-param name="id_db" select="$asses[$assindex]/id_db"/>
		  <xsl:with-param name="bid" select="$bid" />
		</xsl:call-template>
	  </th>
	  <xsl:call-template name="rescell">
		<xsl:with-param name="resindex" select="1"/>
		<xsl:with-param name="assid" select="$asses[$assindex]/id_db"/>
		<xsl:with-param name="bid" select="$bid" />
	  </xsl:call-template>
	</tr>

	<xsl:call-template name="assrow">
	  <xsl:with-param name="assindex" select="$assindex + 1"/>
	  <xsl:with-param name="bid" select="$bid" />
	</xsl:call-template>

  </xsl:if>
</xsl:template>



<xsl:template name="rescell">
  <xsl:param name="resindex">1</xsl:param>
  <xsl:param name="assid">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:variable name="maxindex">
	<xsl:value-of select="count(restable/res/label)+1"/>
  </xsl:variable>
  <xsl:variable name="resval" select="restable/res/value" />

  <xsl:if test="$resindex &lt; $maxindex">

	  <xsl:choose>
		<xsl:when test="$resval[$resindex] &gt; asstable/ass[id_db=$assid]/bands[name='A']/value">
		  <td class="col-a">
			<xsl:call-template name="rescell_value">
			  <xsl:with-param name="assid" select="$assid" />
			  <xsl:with-param name="bid" select="$bid" />
			  <xsl:with-param name="val" select="$resval[$resindex]" />
			</xsl:call-template>
		  </td>
		</xsl:when>
		<xsl:when test="$resval[$resindex] &gt; asstable/ass[id_db=$assid]/bands[name='B']/value">
		  <td class="col-b">
			<xsl:call-template name="rescell_value">
			  <xsl:with-param name="assid" select="$assid" />
			  <xsl:with-param name="bid" select="$bid" />
			  <xsl:with-param name="val" select="$resval[$resindex]" />
			</xsl:call-template>
		  </td>
		</xsl:when>
		<xsl:when test="$resval[$resindex] &gt; asstable/ass[id_db=$assid]/bands[name='C']/value">
		  <td class="col-c">
			<xsl:call-template name="rescell_value">
			  <xsl:with-param name="assid" select="$assid" />
			  <xsl:with-param name="bid" select="$bid" />
			  <xsl:with-param name="val" select="$resval[$resindex]" />
			</xsl:call-template>
		  </td>
		</xsl:when>
		<xsl:otherwise>
		  <td class="col-d">
			<xsl:call-template name="rescell_value">
			  <xsl:with-param name="assid" select="$assid" />
			  <xsl:with-param name="bid" select="$bid" />
			  <xsl:with-param name="val" select="$resval[$resindex]" />
			</xsl:call-template>
		  </td>
		</xsl:otherwise>
	  </xsl:choose>

    <xsl:call-template name="rescell">
	  <xsl:with-param name="resindex" select="$resindex + 1"/>
	  <xsl:with-param name="assid" select="$assid" />
	  <xsl:with-param name="bid" select="$bid" />
	</xsl:call-template>
  </xsl:if>
</xsl:template>

<xsl:template name="rescell_value">
  <xsl:param name="assid">1</xsl:param>
  <xsl:param name="bid"></xsl:param>
  <xsl:param name="val"></xsl:param>

  <xsl:variable name="sidsno">
	<xsl:choose>
	  <xsl:when test="$assid!='-1' and $bid='' and student/assessments/assessment[id_db=$assid]">
		  <xsl:value-of select="count(student[assessments/assessment[id_db=$assid]/value/value=$val])" />
	  </xsl:when>
	  <xsl:when test="$assid!='-1' and student/assessments/assessment[id_db=$assid and subjectcomponent/value=$bid]">
		<xsl:value-of select="count(student[assessments/assessment[id_db=$assid and subjectcomponent/value=$bid]/value/value=$val])" />
	  </xsl:when>
	  <xsl:otherwise>
		0
	  </xsl:otherwise>
	</xsl:choose>
  </xsl:variable>

  <xsl:value-of select="$sidsno" />

  <xsl:if test="$sidsno &gt; 0">
	<ul>
		<xsl:apply-templates select="student[assessments/assessment[id_db=$assid and subjectcomponent/value=$bid]/value/value=$val]/id_db" />
	</ul>
  </xsl:if>

</xsl:template>

<xsl:template match="id_db">
  <li><xsl:value-of select="." /></li>
</xsl:template>


<xsl:template match="student">
	<xsl:element name="li">
	  <xsl:attribute name="id"><xsl:value-of select="id_db/text()" /></xsl:attribute>
	  <xsl:value-of select="displayfullname/value/text()" />
	  <xsl:text>:  </xsl:text>
	  <xsl:apply-templates select="assessments/assessment">
		<xsl:sort select="position()" order="descending" />
	  </xsl:apply-templates>
	</xsl:element>
</xsl:template>

<xsl:template match="assessment">
  <xsl:text>  </xsl:text>
	<xsl:value-of select="result/value/text()" />
</xsl:template>


<xsl:template name="averagerow">
  <xsl:param name="id_db"></xsl:param>
  <xsl:param name="bid"></xsl:param>

  <xsl:variable name="asscount" select="count(student/assessments/assessment[id_db=$id_db][subjectcomponent/value=$bid]/value/value)" />
  <xsl:variable name="banda" select="asstable/ass[id_db=$id_db]/bands[name='A']/value" />
  <xsl:variable name="bandb" select="asstable/ass[id_db=$id_db]/bands[name='B']/value" />
  <xsl:variable name="bandc" select="asstable/ass[id_db=$id_db]/bands[name='C']/value" />
	<xsl:element name="div">
	  <xsl:choose>
		<xsl:when test="$asscount &gt; 0">
			<xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subjectcomponent/value=$bid]/value[value &gt; $bandc]) div $asscount,'##%')" />
			<xsl:text> </xsl:text>
			<xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subjectcomponent/value=$bid]/value[value &gt; $bandb]) div $asscount,'##%')" />
			<xsl:text> </xsl:text>
			<xsl:value-of select="format-number(count(student/assessments/assessment[id_db=$id_db][subjectcomponent/value=$bid]/value[value &gt; $banda]) div $asscount,'##%')" />
		</xsl:when>
		<xsl:otherwise>
			<xsl:text> </xsl:text>
		</xsl:otherwise>
	  </xsl:choose>
	</xsl:element>
</xsl:template>


</xsl:stylesheet>
